<?php

namespace Modules\Login\Services;

use Modules\Login\Models\CargosModel;
use Modules\Login\Models\EmpresaModel;
use Modules\Login\Models\LoginModel;
use Modules\Permissoes\Models\PermissoesModel;

class UserManagement
{
    private $renderFunction;
    private $params;

    public function __construct($renderFunction = 'renderManageUsers', $params = null)
    {
        $this->renderFunction = $renderFunction;
        $this->params = $params;
    }

    public function render($data = [])
    {
        $func = $this->renderFunction;

        return $this->$func($data);
    }

    protected function renderManageUsers($data = [])
    {
        $data['lista_empresas'] = (new EmpresaModel())->Me();
        $data['lista_cargos'] = (new CargosModel())->listForMe();

        return module_view('Login', 'Login/ManageUsers', $data);
    }

    protected function renderCreateEditUser($data = [])
    {
        
        // Check permission
        // $permissoes_model = new PermissoesModel();
        // if (!$permissoes_model->user_has_permission('user.create')) {
        //     return '';
        // }

        // Defaults
        $data['is_editing'] = false;
        $data['usuario'] = null;

        // Load user if editing
        if ($this->params) {
            $loginModel = new LoginModel();
            
            $usuario = $loginModel
                ->select($loginModel->table . '.*, pessoas.id_cargo, pessoas.nome_completo')
                ->join('pessoas', 'pessoas.id_usuario_login = ' . $loginModel->table . '.id_usuario', 'left')
                ->where($loginModel->table . '.id_usuario', $this->params)
                ->first();

            if (!$usuario) {
                return '';
            }

            $data['usuario'] = $usuario;
            $data['is_editing'] = true;
        }

        // Load lists for dropdowns
        $data['lista_empresas'] = (new EmpresaModel())->findAll();
        $data['lista_cargos'] = (new CargosModel())->findAll();

        
        return module_view('Login', 'Login/CreateEdit', $data);
    }

    public function createUser($login, $nome, $senha, $id_empresa, $id_cargo)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Check permission
            $permissionsModel = new PermissoesModel();
            if (!$permissionsModel->user_has_permission('user.create')) {
                return [
                    'success' => false,
                    'message' => 'Você não tem permissão para criar usuários.'
                ];
            }

            // Verify login is unique
            $loginModel = new LoginModel();
            $existingUser = $loginModel->where('login', $login)->first();
            if ($existingUser) {
                return [
                    'success' => false,
                    'message' => 'Este login já está em uso.'
                ];
            }

            // Verify company exists
            $empresaModel = new EmpresaModel();
            $empresa = $empresaModel->find($id_empresa);
            if (!$empresa) {
                return [
                    'success' => false,
                    'message' => 'Empresa não encontrada.'
                ];
            }

            // Verify cargo exists
            $cargoModel = new CargosModel();
            $cargo = $cargoModel->find($id_cargo);
            if (!$cargo) {
                return [
                    'success' => false,
                    'message' => 'Cargo não encontrado.'
                ];
            }

            // Create user
            $userData = [
                'login' => $login,
                'nome' => $nome,
                'senha' => password_hash($senha, PASSWORD_DEFAULT),
                'id_empresa' => $id_empresa,
                'id_cargo' => $id_cargo,
            ];

            $id_usuario = $loginModel->insert($userData);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Erro ao criar usuário.');
            }

            return [
                'success' => true,
                'id_usuario' => $id_usuario,
                'message' => 'Usuário criado com sucesso.'
            ];

        } catch (\Exception $e) {
            $db->transRollback();
            return [
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage()
            ];
        }
    }

    public function updateUser($id_usuario, $nome, $senha = null, $id_empresa, $id_cargo)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Check permission
            $permissionsModel = new PermissoesModel();
            if (!$permissionsModel->user_has_permission('user.edit')) {
                return [
                    'success' => false,
                    'message' => 'Você não tem permissão para editar usuários.'
                ];
            }

            // Verify user exists
            $loginModel = new LoginModel();
            $usuario = $loginModel->find($id_usuario);
            if (!$usuario) {
                return [
                    'success' => false,
                    'message' => 'Usuário não encontrado.'
                ];
            }

            // Verify company exists
            $empresaModel = new EmpresaModel();
            $empresa = $empresaModel->find($id_empresa);
            if (!$empresa) {
                return [
                    'success' => false,
                    'message' => 'Empresa não encontrada.'
                ];
            }

            // Verify cargo exists
            $cargoModel = new CargosModel();
            $cargo = $cargoModel->find($id_cargo);
            if (!$cargo) {
                return [
                    'success' => false,
                    'message' => 'Cargo não encontrado.'
                ];
            }

            // Update user
            $userData = [
                'nome' => $nome,
                'id_empresa' => $id_empresa,
                'id_cargo' => $id_cargo,
            ];

            // Only update password if provided
            if (!empty($senha)) {
                $userData['senha'] = password_hash($senha, PASSWORD_DEFAULT);
            }

            $loginModel->update($id_usuario, $userData);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Erro ao atualizar usuário.');
            }

            return [
                'success' => true,
                'message' => 'Usuário atualizado com sucesso.'
            ];

        } catch (\Exception $e) {
            $db->transRollback();
            return [
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage()
            ];
        }
    }

    public function deleteUser($id_usuario)
    {
        $db = \Config\Database::connect();

        try {
            // Check permission
            $permissionsModel = new PermissoesModel();
            if (!$permissionsModel->user_has_permission('user.delete')) {
                return [
                    'success' => false,
                    'message' => 'Você não tem permissão para deletar usuários.'
                ];
            }

            // Check if user exists
            $loginModel = new LoginModel();
            $usuario = $loginModel->find($id_usuario);
            if (!$usuario) {
                return [
                    'success' => false,
                    'message' => 'Usuário não encontrado.'
                ];
            }

            // Delete the user
            $loginModel->delete($id_usuario);

            return [
                'success' => true,
                'message' => 'Usuário removido com sucesso.'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao remover usuário: ' . $e->getMessage()
            ];
        }
    }
}

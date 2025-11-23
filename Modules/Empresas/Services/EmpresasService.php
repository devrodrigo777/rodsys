<?php

namespace Modules\Empresas\Services;

use Modules\Empresas\Models\EmpresasModel;
use Modules\Permissoes\Models\PermissoesModel;

/**
 * EmpresasService
 * 
 * Serviço responsável pelo gerenciamento de empresas.
 * Implementa:
 * - CRUD completo (criar, listar, editar, deletar)
 * - Validação de CNPJ (formato 14 dígitos + check-digit)
 * - Verificação de CNPJ único no banco
 * - Transações para integridade de dados
 * - Controle de acesso por permissão
 * - Data de adesão automática ao criar
 * 
 * TABELA: empresas
 * - id_empresa (PK, auto)
 * - cnpj (único, 14 dígitos)
 * - razao_social (string, nome da empresa)
 * - plano_ativo (bool, 0=inativo, 1=ativo)
 * - data_adesao (date, auto-preenchido com today())
 * 
 * @package Modules\Empresas\Services
 */
class EmpresasService
{
    private $renderFunction;
    private $params;

    public function __construct($renderFunction = 'renderManageEmpresas', $params = null)
    {
        $this->renderFunction = $renderFunction;
        $this->params = $params;
    }

    public function render($data = [])
    {
        $func = $this->renderFunction;
        return $this->$func($data);
    }

    protected function renderCreateEditEmpresa($data = [])
    {
        $data['is_editing'] = false;
        $data['permissions'] = new PermissoesModel();

        if ($this->params) {
            $empresasModel = new EmpresasModel();
            $data['empresa'] = $empresasModel->find($this->params);
            $data['is_editing'] = true;
        }

        return module_view('Empresas', 'CRUD/CreateEdit', $data);
    }

    protected function renderManageEmpresas($data = [])
    {
        $data['lista_empresas'] = (new EmpresasModel())->listForMe();
        return module_view('Empresas', 'CRUD/Read', $data);
    }

    public function createEmpresa($cnpj, $razao_social, $plano_ativo = 0)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Validate permissions
            $permissionsModel = new PermissoesModel();
            if (!$permissionsModel->user_has_permission('mod.empresas.create') && !$permissionsModel->user_is_superadmin()) {
                return [
                    'success' => false,
                    'message' => 'Você não tem permissão para criar empresas.'
                ];
            }

            $empresasModel = new EmpresasModel();

            // Check if CNPJ already exists
            if ($empresasModel->getEmpresaByCnpj($cnpj)) {
                return [
                    'success' => false,
                    'message' => 'CNPJ já cadastrado no sistema.'
                ];
            }

            $empresaData = [
                'cnpj' => $cnpj,
                'razao_social' => $razao_social,
                'plano_ativo' => (int) $plano_ativo,
                'data_adesao' => date('Y-m-d'),
            ];

            $id_empresa = $empresasModel->insert($empresaData);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Erro ao criar empresa.');
            }

            return [
                'success' => true,
                'id_empresa' => $id_empresa,
                'message' => 'Empresa criada com sucesso.'
            ];

        } catch (\Exception $e) {
            $db->transRollback();
            return [
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage()
            ];
        }
    }

    public function updateEmpresa($id_empresa, $cnpj, $razao_social, $plano_ativo = 0)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {

            $logged_user_empresa = session()->get('id_empresa');

            // Validate permissions
            $permissionsModel = new PermissoesModel();
            if (!$permissionsModel->user_has_permission('mod.empresas.edit') && !$permissionsModel->user_is_superadmin()) {
                return [
                    'success' => false,
                    'message' => 'Você não tem permissão para editar empresas.'
                ];
            }

            // Verificar se a empresa pertence à empresa do usuário logado (a menos que superadmin)
            if(!$permissionsModel->user_is_superadmin()) {
                $empresasModel = new EmpresasModel();
                $empresa = $empresasModel->where('id_empresa', $id_empresa)
                                         ->where('id_empresa', $logged_user_empresa)
                                         ->first();
                if (!$empresa) {
                    return [
                        'success' => false,
                        'message' => 'Empresa não encontrada ou você não tem permissão para editá-la.'
                    ];
                }
            }
            

            $empresasModel = new EmpresasModel();

            // Check if empresa exists
            if(!$permissionsModel->user_is_superadmin()) {
                $empresa = $empresasModel->where('id_empresa', $id_empresa)
                                         ->where('id_empresa', $logged_user_empresa)
                                         ->first();
            } else {
                $empresa = $empresasModel->find($id_empresa);
            }

            if (!$empresa) {
                return [
                    'success' => false,
                    'message' => 'Empresa não encontrada.'
                ];
            }

            // Check if CNPJ is already used by another company
            $existing = $empresasModel->where('cnpj', $cnpj)->where('id_empresa !=', $id_empresa)->first();
            if ($existing) {
                return [
                    'success' => false,
                    'message' => 'CNPJ já cadastrado para outra empresa.'
                ];
            }

            $empresaData = [
                'cnpj' => $cnpj,
                'razao_social' => $razao_social,
                'plano_ativo' => (int) $plano_ativo,
            ];


            $empresasModel->update($id_empresa, $empresaData);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Erro ao atualizar empresa.');
            }

            return [
                'success' => true,
                'message' => 'Empresa atualizada com sucesso.'
            ];

        } catch (\Exception $e) {
            $db->transRollback();
            return [
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage()
            ];
        }
    }

    public function deleteEmpresa($id_empresa)
    {
        $db = \Config\Database::connect();

        try {
            // Validate permissions
            $permissionsModel = new PermissoesModel();
            if (!$permissionsModel->user_has_permission('mod.empresas.delete') && !$permissionsModel->user_is_superadmin()) {
                return [
                    'success' => false,
                    'message' => 'Você não tem permissão para deletar empresas.'
                ];
            }

            $empresasModel = new EmpresasModel();
            $logged_user_empresa = session()->get('id_empresa');

            // Check if empresa exists
            if(!$permissionsModel->user_is_superadmin()) {
                $empresa = $empresasModel->where('id_empresa', $id_empresa)
                                         ->where('id_empresa', $logged_user_empresa)
                                         ->first();
            } else {
                $empresa = $empresasModel->find($id_empresa);
            }

            if (!$empresa) {
                return [
                    'success' => false,
                    'message' => 'Empresa não encontrada.'
                ];
            }

            // verificaremos se existem usuarios, cargos e permissões vinculadas a esta empresa antes de deletar
            //  Se existirem, teremos que deletar tudo que tem associação a essa empresa, pois tudo é foreign key
            // Check for associated users, roles, and permissions
            $usuariosModel = new \Modules\Login\Models\PessoasModel();
            $loginModel = new \Modules\Login\Models\LoginModel();
            $cargosModel = new \Modules\Departments\Models\DepartmentModel();

            $usuariosAssociados = $usuariosModel->where('id_empresa', $id_empresa)->findAll();
            $cargosAssociados = $cargosModel->where('id_empresa', $id_empresa)->findAll();
            $loginsAssociados = $loginModel->where('id_empresa', $id_empresa)->findAll();

            if($loginsAssociados) {
                foreach($loginsAssociados as $login) {
                    $loginModel->delete($login['id_usuario']);
                }
            }

            // Delete associated records in cascade
            if ($usuariosAssociados) {
                foreach ($usuariosAssociados as $usuario) {
                    $usuariosModel->delete($usuario['id_usuario']);
                }
            }

            if ($cargosAssociados) {
                foreach ($cargosAssociados as $cargo) {
                    $cargosModel->delete($cargo['id_cargo']);
                }
            }

            // Delete the empresa
            $empresasModel->delete($id_empresa);

            return [
                'success' => true,
                'message' => 'Empresa removida com sucesso.'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao remover empresa: ' . $e->getMessage()
            ];
        }
    }
}

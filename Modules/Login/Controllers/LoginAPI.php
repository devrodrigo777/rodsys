<?php

namespace Modules\Login\Controllers;

use Modules\Login\Libraries\Passlib;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Modules\Login\Models\LoginModel;
use Modules\Login\Models\PessoasModel;
use Modules\Login\Models\CargosModel;

use Modules\Permissoes\Models\PermissoesModel;

class LoginAPI extends ResourceController
{
    use ResponseTrait;

    /**
     * Permissions model instance.
     *
     * @var \Modules\Permissoes\Models\PermissoesModel
     */
    protected $permissionsModel;

    public function __construct()
    {
        $this->permissionsModel = new PermissoesModel();
    }

    /**
     * Retorna a lista de usuários com dados de suas empresas e cargos.
     * @return ResponseInterface
     */
    public function userList(): ResponseInterface
    {
        require_once APPPATH . 'ThirdParty/ssp.class.php';

        $permissionsModel = $this->permissionsModel;
        if(!$this->permissionsModel->user_has_permission('mod.user.view')
            && !$this->permissionsModel->user_is_superadmin())
        {
            return $this->failForbidden('You do not have permission to view users.');
        }

        $db = db_connect();
        $db_details = [
            'user' => $db->username,
            'pass' => $db->password,
            'db'   => $db->database,
            'host' => $db->hostname,
        ];

        $table = 'pessoas';
        $primaryKey = 'id_pessoa';

        

        $whereClause = null;
        // Defina a Cláusula FROM/JOIN que você precisa:
        $joinClause = "
            JOIN login lg ON pessoas.id_usuario_login = lg.id_usuario
            JOIN cargos c ON pessoas.id_cargo = c.id_cargo
            JOIN empresas e ON pessoas.id_empresa = e.id_empresa
        ";

        // filtrar somente usuários da empresa do usuário logado, se não for superadmin
        if (! $this->permissionsModel->user_is_superadmin() && !$this->permissionsModel->user_has_permission('mod.user.company.listall')) {
            $id_empresa_logada = session()->get('id_empresa');
            $whereClause = "e.id_empresa = " . intval($id_empresa_logada);
        }

        // obter o valor de busca
        $whereClause .= ($_GET['search']['value'] ?? '') !== '' ? " AND (
            pessoas.nome_completo LIKE '%" . $db->escapeLikeString($_GET['search']['value']) . "%' OR
            c.nome LIKE '%" . $db->escapeLikeString($_GET['search']['value']) . "%' OR
            e.razao_social LIKE '%" . $db->escapeLikeString($_GET['search']['value']) . "%'
        )" : null;

        $columns = array(
            [ 'db' => 'id_usuario_login', 'dt' => 9 ],
            [ 'db' => 'nome_completo',  'dt' => 0 ],
            
            [ 'db' => 'nome',          'dt' => 1 ],
            
            [ 'db' => 'razao_social',  'dt' => 2 ],

            [ 'db' => 'is_global',    'dt' => 4  ],

            [ 'db' => 'id_pessoa',         'dt' => 3,
              'formatter' => function($id, $row) use ($permissionsModel) {

                    $id_pessoa = $id;
                    $is_global = $row['is_global'];
                    $html_actions = '';

                    // Antes de exibir os menus, verificaremos se o usuário que
                    // ele está visualizando não é ele mesmo (não pode se deletar)
                    $id_usuario_logado = session()->get('usuario');

                    if($id_usuario_logado != $row['id_usuario_login']) {
                        if($permissionsModel->user_has_permission('mod.user.edit')) {
                        $html_actions .= '
                            <button class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Editar" onclick="editUser(' . $row['id_usuario_login'] . ')"><i class="fa fa-edit"></i></button>
                            ';
                        }

                        if($permissionsModel->user_has_permission('mod.user.delete')) {
                            if ($is_global) {
                                $html_actions .= '
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Remover" onclick="deleteUser(' . $id_pessoa . ')"><i class="fa fa-trash"></i></button>
                                ';
                            }   
                        }
                    }


                  return $html_actions;
              }
            ]
        );

        $result = \SSP::complex($_GET, $db_details, $table, $primaryKey, $columns, $whereClause, null, $joinClause);

        return $this->respond($result);
    }

    /** Atualização de usuarios existentes */
    public function update($id_usuario = null)
    {
       

        // Este endpoint vem de um formulário — usar redirecionamentos com flashdata
        if (!is_numeric($id_usuario)) {
            session()->setFlashdata('user.feedback', 'ID de usuário inválido.');
            return redirect()->back()->withInput();
        }

        // Verifica se tem permissão para editar
        $permissoesModel = new PermissoesModel();

        if (! $permissoesModel->user_has_permission('mod.user.edit')) {
            session()->setFlashdata('user.feedback', 'Você não tem permissão para editar usuários.');
            return redirect()->to('dashboard/acessos/usuarios');
        }

        $loginModel = new LoginModel();
        $pessoasModel = new PessoasModel();
        $superadmin = $permissoesModel->user_is_superadmin();

        // Verificaremos se o usuario que ele está tentando editar é ele mesmo
        $id_usuario_logado = session()->get('usuario');
        $where_id_empresa = $superadmin ? ['id_empresa >' => 0] : ['id_empresa' => session()->get('id_empresa')];

        // echo $id_empresa_logada;die();
        if($id_usuario_logado == $id_usuario) {
            session()->setFlashdata('user.feedback', 'Você não pode editar seu próprio usuário por este formulário.');
            return redirect()->to('dashboard/acessos/usuarios');
        }

        
        // Busca o usuário de login (por id_usuario)
        // Para prevenir de editar usuario que nao faz parte da equipe caso nao seja  admin
        // Iremos acrescentar uma verificação extra
        // Iremos prevenir também que ele edite ele mesmo.
        $usuario = $loginModel
        ->where('id_usuario', $id_usuario)
        ->where($where_id_empresa)
        // whereNot usuario seja ele mesmo
        ->where('id_usuario !=', $id_usuario_logado)
        ->first();

        if (!$usuario) {
            session()->setFlashdata('user.feedback', 'Usuário não encontrado.');
            return redirect()->to('dashboard/acessos/usuarios');
        }

        // Recebe parâmetros via POST
        $inputNome = $this->request->getVar('inputNome');
        $inputLogin = $this->request->getVar('inputLogin');
        $inputSenha = $this->request->getVar('inputSenha');
        $inputEmpresa = $this->request->getVar('inputEmpresa');
        $inputCargo = $this->request->getVar('inputCargo');

        // Preparar updates
        $loginUpdate = [];
        $pessoaUpdate = [];

        if ($inputLogin) {
            $loginUpdate['usuario'] = strtoupper($inputLogin);
        }

        if ($inputEmpresa) {
            // atualizar também na tabela login (campo id_empresa)
            $loginUpdate['id_empresa'] = $inputEmpresa;
            $pessoaUpdate['id_empresa'] = $inputEmpresa;
        }

        if ($inputSenha) {
            // usar Passlib para hashear a senha
            $passlib = new Passlib();
            $loginUpdate['senha_hash'] = $passlib->hashPassword($inputSenha);
        }

        if ($inputNome) {
            $pessoaUpdate['nome_completo'] = strtoupper($inputNome);
        }

        if ($inputCargo) {
            $pessoaUpdate['id_cargo'] = $inputCargo;
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // Atualizar tabela login (somente campos fornecidos)
            if (!empty($loginUpdate)) {
                $loginModel->update($id_usuario, $loginUpdate);
            }

            // Atualizar tabela pessoas (por id_usuario_login)
            if (!empty($pessoaUpdate)) {
                $pessoasModel->where('id_usuario_login', $id_usuario)->set($pessoaUpdate)->update();
            }

            $db->transCommit();

            session()->setFlashdata('user.feedback', 'Usuário atualizado com sucesso.');
            return redirect()->to('dashboard/acessos/usuarios');

        } catch (\Exception $e) {
            $db->transRollback();
            session()->setFlashdata('user.feedback', 'Erro ao atualizar usuário: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Delete de usuarios via API
     */
    public function deleteUser($id_pessoa= null): ResponseInterface
    {
        
        if (!is_numeric($id_pessoa)) {
            return $this->fail('Invalid user ID provided.', ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Verifica se o tem permissão para deletar
        $permissoesModel = new PermissoesModel();

        if (! $permissoesModel->user_has_permission('mod.user.delete') && ! $permissoesModel->user_is_superadmin()) {
            return $this->failForbidden('You do not have permission to delete users.');
        }

        $pessoasModel = new PessoasModel();
        $loginModel = new LoginModel();

        // Se não for superadmin, garantir que o usuário pertence à mesma empresa
        // E também garantir que não seja ele mesmo.
        if (! $permissoesModel->user_is_superadmin()) {
            $id_empresa_logada = session()->get('id_empresa');
            $pessoa = $pessoasModel->where('id_empresa', $id_empresa_logada)
                                   ->where('id_pessoa', $id_pessoa)
                                   // whereNot seja ele mesmo
                                   ->where('id_usuario_login !=', session()->get('usuario'))
                                   ->first();
        } else {
            $pessoa = $pessoasModel->find($id_pessoa);
        }

        if (!$pessoa) {
            return $this->failNotFound('User not found');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // Deletar da tabela pessoas
            $pessoasModel->delete($id_pessoa);

            // Deletar da tabela login
            $loginModel->delete($pessoa['id_usuario_login']);

            $db->transCommit();
            return $this->respondDeleted(['message' => 'User deleted successfully']);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->failServerError('Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Cria uma nova empresa (da tabela empresas).
     *
     * @return ResponseInterface
     */
    public function createCompany(): ResponseInterface
    {
        // Verificar se o usuario possui permissões para criar uma nova empresa
        if(!$this->permissionsModel->user_has_permission('mod.empresas.create') && !$this->permissionsModel->user_is_superadmin()) {
            $this->fail('Você não tem permissão para criar uma nova empresa.', 403);
            exit;
        }

        $rules = [
            'cnpj'        => 'required|exact_length[14]|is_unique[empresas.cnpj]',
            'razao_social' => 'required|min_length[3]|max_length[255]',
            'data_adesao'  => 'required|valid_date', // Formato AAAA-MM-DD
        ];

        if (! $this->validate($rules)) {
            return $this->fail($this->validator->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
        }

        $data = [
            'cnpj'        => $this->request->getVar('cnpj'),
            'razao_social' => $this->request->getVar('razao_social'),
            'data_adesao'  => $this->request->getVar('data_adesao'),
        ];

        $model = new \Modules\Login\Models\EmpresaModel(); // Assumindo que você tem um modelo para Empresas
        $model->insert($data);

        return $this->respondCreated(['message' => 'Company created successfully']);
    }

    /**
     * Cria um novo usuário (da tabela login).
     *
     * @return ResponseInterface
     */
    public function createUser(): ResponseInterface
    {
        // Verificar se o usuario possui permissões para criar u novo usuário.
        if(!$this->permissionsModel->user_has_permission('mod.user.create') && !$this->permissionsModel->user_is_superadmin()) {
            $this->fail('Você não tem permissão para criar um  novo usuário.', 403);
            exit;
        }

        $rules = [
            'usuario' => 'required|min_length[3]|max_length[100]',
            'id_empresa' => 'required|integer',
            'login'   => 'required|min_length[5]|max_length[50]|is_unique[login.usuario,id_empresa,{id_empresa}]',
            'password'   => 'required|min_length[4]',
        ];

        if (! $this->validate($rules)) {
            return $this->fail($this->validator->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
        }

        $passlib = new Passlib();

        $data = [
            'id_empresa' => $this->request->getVar('id_empresa'),
            'usuario'   => strtoupper($this->request->getVar('login')),
            'senha_hash'   => $passlib->hashPassword($this->request->getVar('password')),
            'ativo'      => 1,
        ];

        
        $loginModel = new LoginModel();
        $pessoasModel = new PessoasModel();
        $cargosModel = new CargosModel();

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // Inserir na tabela login
            $loginId = $loginModel->insert($data, true); // true para retornar o ID inserido

            if (!$loginId) {
                throw new \Exception('Failed to create login entry.');
            }

            // Preparar dados para a tabela pessoas
            $pessoaData = [
                'id_empresa' => $this->request->getVar('id_empresa'),
                'id_usuario_login' => $loginId,
                'nome_completo' => strtoupper($this->request->getVar('usuario')), // Usar o nome completo do usuário
                'id_cargo' => $this->request->getVar('id_cargo'), // Adicionar o ID do cargo
            ];

            
            // Inserir na tabela pessoas
            $pessoaId = $pessoasModel->insert($pessoaData, true);

            if (!$pessoaId) {
                throw new \Exception('Failed to create pessoa entry.');
            }

            $db->transCommit();
            return $this->respondCreated(['message' => 'User created successfully']);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->failServerError('Failed to create user: ' . $e->getMessage());
        }
    }



    /**
     * Realiza o login do usuário.
     *
     * @return ResponseInterface
     */
    public function login(): ResponseInterface
    {
        $rules = [
            'id_empresa' => 'required|integer',
            'usuario'   => 'required|min_length[5]|max_length[50]',
            'senha'   => 'required|min_length[8]',
        ];

        if (! $this->validate($rules)) {
            return $this->fail($this->validator->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
        }
        // Lógica de autenticação aqui...
        return $this->respond(['message' => 'Login successful']);
    }
}

// End of File

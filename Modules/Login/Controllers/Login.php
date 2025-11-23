<?php

namespace Modules\Login\Controllers;

use App\Controllers\BaseController;
use Modules\Login\Services\AuthService;
use Modules\Login\Services\UserManagement;
use Modules\Dashboard\Controllers\Dashboard;


class Login extends BaseController
{
    /**
     * Permissions model instance.
     *
     * @var \Modules\Permissoes\Models\PermissoesModel
     */
    protected $permissionsModel;

    public function __construct()
    {
        $this->permissionsModel = new \Modules\Permissoes\Models\PermissoesModel();
    }

    public function index()
    {
        return module_view('Login', 'Login\Auth');
    }

    public function manage()
    {
        $dashboard = new Dashboard();

        $dashboard->__set_module_vars([
            'module_view_data' => [
                'service_view' => new UserManagement(),
                'before_css' => [
                    'https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.css'
                ],
                'custom_js' => [
                     'https://code.jquery.com/jquery-3.7.1.js',
                     'https://cdn.datatables.net/2.3.4/js/dataTables.js',
                     base_url('assets/js/datatables/login_userlist.js'),
                     base_url("assets/js/login_module/manage-users.js")
                ]
            ]
        ]);

        return $dashboard->_render();
    }

    /**
     * Rota para editar um usuário existente
     */
    public function edit($id = null)
    {

        // Verificar permissões de editar um usuario aqui, se necessário
        if(!$this->permissionsModel->user_has_permission('mod.user.edit') && !$this->permissionsModel->user_is_superadmin()) {
            return redirect()->to('/dashboard/acessos/usuarios')->with('error', 'Você não tem permissão para editar este usuário.');
        }

        // Verificação prévia para garantir que o usuário pode editar o usuário com o ID fornecido
        if(!$this->permissionsModel->user_is_superadmin()) {
            $loginModel = new \Modules\Login\Models\LoginModel();
            $usuario = $loginModel
                ->where("id_empresa", session()->get('id_empresa'))
                ->where("id_usuario", $id)
                // whereNot seja ele mesmo
                ->where('id_usuario !=', session()->get('usuario'))
                ->first();

            if ($usuario === null) {
                session()->setFlashdata('user.feedback.icon', 'error');
                return redirect()->to('dashboard/acessos/usuarios')->with('user.feedback', 'Você não tem permissão para editar este usuário.');
            }
        }

        $dashboard = new Dashboard();

        $dashboard->__set_module_vars([
            'module_view_data' => [
                'service_view' => new UserManagement('renderCreateEditUser', $id),
                'before_css' => [
                    'https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css'
                ],
                'custom_js' => [
                     'https://cdn.jsdelivr.net/npm/simple-datatables@latest',
                     base_url('assets/js/datatables/login_userlist.js'),
                     base_url("assets/js/login_module/edit-user.js")
                ]
            ]
        ]);

        return $dashboard->_render();
    }

    public function newUser()
    {
        // Verificar permissões de criar um usuario aqui, se necessário
        if(!$this->permissionsModel->user_has_permission('mod.user.edit') && !$this->permissionsModel->user_is_superadmin()) {
            return redirect()->to('/dashboard/acessos/usuarios')->with('error', 'Você não tem permissão para criar um novo usuário.');
        }

        $dashboard = new Dashboard();

        $dashboard->__set_module_vars([
            'module_view_data' => [
                'service_view' => new UserManagement('renderCreateEditUser'),
                'before_css' => [
                    'https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css'
                ],
                'custom_js' => [
                     'https://cdn.jsdelivr.net/npm/simple-datatables@latest',
                     base_url('assets/js/datatables/login_userlist.js')
                ]
            ]
        ]);

        return $dashboard->_render();
    }

    public function logout()
    {
        $authService = new AuthService();
        $authService->logout();

        return redirect()->to(base_url('/login'))->with('login_message', 'Obrigado pelo seu acesso!');
    }

    public function authenticate()
    {
        // Regras de validação para o formulário de login
        $rules = [
            'id_empresa' => 'required|integer',
            'usuario'   => 'required|min_length[5]|max_length[50]',
            'senha'   => 'required|min_length[3]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                    ->withInput()
                    ->with('login_message', 'Usuário ou senha incorretos.');
        }

        // Obtém os dados do formulário pós-validação
        $idEmpresa = $this->request->getPost('id_empresa');
        $usuario = $this->request->getPost('usuario');
        $senha = $this->request->getPost('senha');

        // Regra de negócio de autenticação
        $authService = new AuthService();
        $user = $authService->authenticate($idEmpresa, $usuario, $senha);

        if (! $user) {
            return redirect()->back()->withInput()->with('login_message', 'Usuário ou senha incorretos.');
        }

        // Autenticação bem-sucedida, você pode definir uma sessão aqui
        $authService->setSession($user);

        return redirect()->to(base_url('dashboard')); // Redirecionar para o dashboard ou página inicial
    }

}
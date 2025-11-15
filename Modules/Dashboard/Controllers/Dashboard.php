<?php

namespace Modules\Dashboard\Controllers;

use App\Controllers\BaseController;
use Modules\Login\Services\AuthService;
use Modules\Login\Services\PessoasService;
use App\Models\ModuloModel;

use Modules\Dashboard\Services\MainDashboard;
use Modules\Permissoes\Models\PermissoesModel;


class Dashboard extends BaseController
{

    private $pessoasService;
    private $moduloModel;
    private $module_vars = [];

    public function __construct()
    {
        $this->pessoasService = new PessoasService();
        $this->moduloModel = new ModuloModel();
    }

    public function __set_module_vars($var = [])
    {
        $this->module_vars = $var;
    }

    public function index()
    {
        $this->__set_module_vars([
            'module_view_data' => [
                'service_view' => new MainDashboard()
            ]
        ]);

        return $this->_render();
    }

    public function _render($data = [])
    {
        // Verifica Session antes de Prosseguir
        if(!(new AuthService())->is_logged_in()) {
            return redirect()->to(base_url('/login'));
        }

        $usuarioLogado = $this->pessoasService->getPessoaLogada();
        
        // Listar módulos ativos do model ModuloModel
        $modulosAtivos = $this->moduloModel->listarModulosAtivos();

        $dadosView = $this->module_vars['module_view_data'] ?? [];
        
        // return view('Modules\Dashboard\Views\Dashboard');
        return module_view('Dashboard', 'Main', [
            'usuario' => $usuarioLogado,
            'GLOBAL_ERPVARS' => [
                "LOGIN" => $usuarioLogado
            ],
            'modules' => $modulosAtivos,
            'permissoes' => new PermissoesModel(),
            ...$dadosView
        ]);
    }
}

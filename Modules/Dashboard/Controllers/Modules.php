<?php

namespace Modules\Dashboard\Controllers;

use App\Controllers\BaseController;
use Modules\Dashboard\Controllers\Dashboard;
use Modules\Permissoes\Models\PermissoesModel;
use Modules\Dashboard\Services\ModulesService;

class Modules extends BaseController
{
    private $permissionsModel;

    public function __construct()
    {
        $this->permissionsModel = new PermissoesModel();
    }

    // Visualizar módulos para uma empresa específica
    //
    public function forCompanies($company_id)
    {
        $dashboard = new Dashboard();

        // Verifica se o usuário tem permissão para visualizar módulos
        if (!$this->permissionsModel->user_has_permission('mod.modules.view') && 
            !$this->permissionsModel->user_is_superadmin()) {
            return redirect()->to(base_url('/dashboard'))->with('error', 'Você não tem permissão para acessar o visualizador de módulos.');
        }

        $dashboard->__set_module_vars([
            'module_view_data' => [
                'service_view' => new ModulesService('forCompanies', $company_id),
                'before_css' => [
                    'https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.css'
                ],
                'custom_js' => [
                     'https://code.jquery.com/jquery-3.7.1.js',
                     'https://cdn.datatables.net/2.3.4/js/dataTables.js',
                     base_url("assets/dashboard/js/modules_forcompanies.js")
                ]
            ]
        ]);

        return $dashboard->_render();
    }

    public function index()
    {
        $dashboard = new Dashboard();

        // Verifica se o usuário tem permissão para visualizar módulos
        if (!$this->permissionsModel->user_has_permission('mod.modules.view') && 
            !$this->permissionsModel->user_is_superadmin()) {
            return redirect()->to(base_url('/dashboard'))->with('error', 'Você não tem permissão para acessar o visualizador de módulos.');
        }

        $dashboard->__set_module_vars([
            'module_view_data' => [
                'service_view' => new ModulesService('dashboard'),
                'before_css' => [
                    'https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.css'
                ],
                'custom_js' => [
                     'https://code.jquery.com/jquery-3.7.1.js',
                     'https://cdn.datatables.net/2.3.4/js/dataTables.js',
                     base_url("assets/dashboard/js/modules.js")
                ]
            ]
        ]);

        return $dashboard->_render();
    }
}
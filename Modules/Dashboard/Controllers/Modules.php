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
        $logged_company = session()->get('id_empresa');
        $authorized = false;

        // Se não for superadmin, verifica se o usuário pertence à empresa solicitada
        if (!$this->permissionsModel->user_is_superadmin()) {
            if ($logged_company == $company_id)
                $authorized = true;
        } else {
            $authorized = true; // Superadmin tem acesso a todas as empresas
        }

        // Verifica se o usuário tem permissão para visualizar módulos
        if ((!$this->permissionsModel->user_has_permission('mod.modules.view') && 
            !$this->permissionsModel->user_is_superadmin()) ||
            !$authorized) {
            return redirect()->to(base_url('/dashboard/modulos'))->with('icon', 'error')->with('department.feedback.success', 'Você não tem permissão para acessar os módulos desta empresa.');
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
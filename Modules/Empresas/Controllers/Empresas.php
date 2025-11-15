<?php

namespace Modules\Empresas\Controllers;

use CodeIgniter\Controller;
use Modules\Empresas\Services\EmpresasService;
use Modules\Permissoes\Models\PermissoesModel;
use Modules\Dashboard\Controllers\Dashboard;

/**
 * Empresas Controller
 * 
 * Responsável pelo roteamento e renderização das views de gerenciamento de empresas.
 * 
 * PADRÃO: Dashboard Module Pattern
 * - Usa Dashboard::__set_module_vars() para passar dados ao template
 * - Delegación de lógica para Service layer
 * - Validação de permissões antes de cada ação
 * - Carrega assets (CSS/JS) condicionalmente
 * 
 * ROTAS DISPONÍVEIS:
 * - GET  /dashboard/empresas → index() - Listagem com DataTables
 * - GET  /dashboard/empresas/novo → novo() - Formulário criar
 * - GET  /dashboard/empresas/:id → editar($id) - Formulário editar
 * 
 * @package Modules\Empresas\Controllers
 */
class Empresas extends Controller
{
    public function index()
    {
        $permissionsModel = new PermissoesModel();

        if (!$permissionsModel->user_has_permission('empresas.view') && !$permissionsModel->user_is_superadmin()) {
            return redirect()->to('dashboard')->with('error', 'Acesso negado');
        }

        $dashboard = new Dashboard();
        $dashboard->__set_module_vars([
            'module_view_data' => [
                'service_view' => new EmpresasService('renderManageEmpresas'),
                'before_css' => [
                    'https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.css'
                ],
                'custom_js' => [
                    'https://code.jquery.com/jquery-3.7.1.js',
                    'https://cdn.datatables.net/2.3.4/js/dataTables.js',
                    base_url('assets/empresas_module/js/read.js')
                ]
            ]
        ]);

        return $dashboard->_render();
    }

    public function novo()
    {
        $permissionsModel = new PermissoesModel();

        if (!$permissionsModel->user_has_permission('empresas.create') && !$permissionsModel->user_is_superadmin()) {
            return redirect()->to('dashboard/empresas')->with('error', 'Acesso negado');
        }

        $dashboard = new Dashboard();
        $dashboard->__set_module_vars([
            'module_view_data' => [
                'service_view' => new EmpresasService('renderCreateEditEmpresa'),
                'custom_js' => [
                    base_url('assets/empresas_module/js/createEdit.js')
                ]
            ]
        ]);

        return $dashboard->_render();
    }

    public function editar($id = null)
    {
        $permissionsModel = new PermissoesModel();

        if (!$permissionsModel->user_has_permission('empresas.edit') && !$permissionsModel->user_is_superadmin()) {
            return redirect()->to('dashboard/empresas')->with('error', 'Acesso negado');
        }

        if (!$id) {
            return redirect()->to('dashboard/empresas');
        }

        $dashboard = new Dashboard();
        $dashboard->__set_module_vars([
            'module_view_data' => [
                'service_view' => new EmpresasService('renderCreateEditEmpresa', $id),
                'custom_js' => [
                    base_url('assets/empresas_module/js/createEdit.js')
                ]
            ]
        ]);

        return $dashboard->_render();
    }
}

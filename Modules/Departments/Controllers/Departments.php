<?php

namespace Modules\Departments\Controllers;

use App\Controllers\BaseController;
use Modules\Departments\Models\DepartmentModel;
use Modules\Dashboard\Controllers\Dashboard;
use Modules\Departments\Services\DepartmentService;

class Departments extends BaseController
{
    protected $departmentModel;
    protected $permissionsModel;

    public function __construct()
    {
        $this->departmentModel = new DepartmentModel();
        $this->permissionsModel = new \Modules\Permissoes\Models\PermissoesModel();
    }

    public function novo()
    {
        $dashboard = new Dashboard();

        // Verificar permissões de criar um novo departamento aqui, se necessário
        if(!$this->permissionsModel->user_has_permission('mod.departments.create') && !$this->permissionsModel->user_is_superadmin()) {
            return redirect()->to('dashboard/departamentos')->with('error', 'Você não tem permissão para criar um novo departamento.');
        }

        $dashboard->__set_module_vars([
            'module_view_data' => [
                'service_view' => new DepartmentService('renderCreateEditDepartment'),
                'before_css' => [
//                    'https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.css'
                ],
                'custom_js' => [
                     'https://code.jquery.com/jquery-3.7.1.js',
                     //'https://cdn.datatables.net/2.3.4/js/dataTables.js',
                     //base_url('assets/js/datatables/departamentos_read.js'),
                     base_url("assets/departments_module/js/createEdit.js")
                ]
            ]
        ]);

        return $dashboard->_render();
    }

    public function index()
    {
        $dashboard = new Dashboard();

        $dashboard->__set_module_vars([
            'module_view_data' => [
                'service_view' => new DepartmentService(),
                'before_css' => [
                    'https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.css'
                ],
                'custom_js' => [
                     'https://code.jquery.com/jquery-3.7.1.js',
                     'https://cdn.datatables.net/2.3.4/js/dataTables.js',
                     base_url('assets/js/datatables/departamentos_read.js'),
                     base_url("assets/departments_module/js/read.js")
                ]
            ]
        ]);

        return $dashboard->_render();
    }

    public function api_read()
    {
        $departments = $this->departmentModel->findAll();
        return $this->response->setJSON($departments);
    }

    public function create()
    {
        return view('Modules\Departments\Views\create');
    }

    public function store()
    {
        if (!$this->validate([
            'department_name' => 'required|min_length[3]|max_length[255]',
            'department_description' => 'max_length[500]'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->departmentModel->save([
            'department_name' => $this->request->getPost('department_name'),
            'department_description' => $this->request->getPost('department_description')
        ]);

        return redirect()->to('/departments')->with('success', 'Department created successfully.');
    }

    public function edit($id)
    {
        $dashboard = new Dashboard();

        // Verificar permissões de editar o departamento aqui, se necessário
        if(!$this->permissionsModel->user_has_permission('mod.departments.edit') && !$this->permissionsModel->user_is_superadmin()) {
            return redirect()->to('/departments')->with('error', 'Você não tem permissão para editar este departamento.');
        }

        $dashboard->__set_module_vars([
            'module_view_data' => [
                'service_view' => new DepartmentService('renderCreateEditDepartment', $id),
                'before_css' => [
                    // 'https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.css'
                ],
                'custom_js' => [
                     'https://code.jquery.com/jquery-3.7.1.js',
                     // 'https://cdn.datatables.net/2.3.4/js/dataTables.js',
                     // base_url('assets/js/datatables/departamentos_read.js'),
                     base_url("assets/departments_module/js/createEdit.js")
                ]
            ]
        ]);

        
        return $dashboard->_render();
    }

    public function update($id)
    {
        
        // Verificar permissões de atualizar o departamento aqui, se necessário
        if(!$this->permissionsModel->user_has_permission('mod.departments.edit') || !$this->permissionsModel->user_is_superadmin()) {
            return redirect()->to('/departments')->with('error', 'Você não tem permissão para editar este departamento.');
        }

        if (!$this->validate([
            'department_name' => 'required|min_length[3]|max_length[255]',
            'department_description' => 'max_length[500]'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->departmentModel->update($id, [
            'department_name' => $this->request->getPost('department_name'),
            'department_description' => $this->request->getPost('department_description')
        ]);
        return redirect()->to('/departments')->with('success', 'Department updated successfully.');
    }
}
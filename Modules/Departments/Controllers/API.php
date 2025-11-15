<?php

namespace Modules\Departments\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

use Modules\Permissoes\Models\PermissoesModel;
use Modules\Departments\Services\DepartmentService;
use Modules\Departments\Models\DepartmentModel;

class API extends ResourceController
{
    
    use ResponseTrait;

    public function create()
    {
        // Get POST data
        $nome = $_POST['inputNome'] ?? null;
        $descricao = $_POST['inputDescricao'] ?? null;
        $permissoes = $_POST['permissoes'] ?? [];

        // Use DepartmentService to create cargo with permissions
        $departmentService = new DepartmentService();
        $result = $departmentService->createWithPermissions($nome, $descricao, $permissoes);

        if ($result['success']) {
            session()->setFlashdata('department.feedback.success', $result['message']);
            return redirect()->to('dashboard/departamentos');
        } else {
            session()->setFlashdata('department.feedback.error', $result['message']);
            return redirect()->back()->withInput();
        }
    }


    /**
     * Atualiza um departamento existente.
     */
    public function update($id = null)
    {
        // Get POST data
        $nome = $_POST['inputNome'] ?? null;
        $descricao = $_POST['inputDescricao'] ?? null;
        $permissoes = $_POST['permissoes'] ?? [];

        // Verificações de segurança
        if (!$id) {
            session()->setFlashdata('department.feedback.error', 'ID do departamento não fornecido.');
            return redirect()->back()->withInput();
        }

        



        // Use DepartmentService to update cargo with permissions
        $departmentService = new DepartmentService();
        $result = $departmentService->updateWithPermissions($id, $nome, $descricao, $permissoes);

        if ($result['success']) {
            session()->setFlashdata('department.feedback.success', $result['message']);
            return redirect()->to('dashboard/departamentos');
        } else {
            session()->setFlashdata('department.feedback.error', $result['message']);
            return redirect()->back()->withInput();
        }
    }
    
    /**
     * Remove um departamento existente.
     */
        public function delete($id = null)
        {
            if (!$id) {
                return $this->fail('ID do departamento não fornecido.', 400);
            }
    
            $departmentService = new DepartmentService();
            $result = $departmentService->deleteDepartment($id);
    
            if ($result['success']) {
                return $this->respond(['message' => $result['message']], 200);
            } else {
                return $this->fail($result['message'], 500);
            }
        }

    /**
     * Retorna a lista de usuários com dados de suas empresas e cargos.
     * @return ResponseInterface
     */
    public function list(): ResponseInterface
    {
        require_once APPPATH . 'ThirdParty/ssp.class.php';

        $db = db_connect();
        $db_details = [
            'user' => $db->username,
            'pass' => $db->password,
            'db'   => $db->database,
            'host' => $db->hostname,
        ];

        $table = 'cargos';
        $primaryKey = 'id_cargo';

        $id_empresa = session()->get('id_empresa');
        $permissionsModel = new PermissoesModel();

        $joinClause = '';
        // Defina a Cláusula FROM/JOIN que você precisa:
        // $joinClause = "
        //     JOIN cargos c ON pessoas.id_cargo = c.id_cargo
        //     JOIN empresas e ON pessoas.id_empresa = e.id_empresa
        // ";

        // Variável where para filtrar resultado. Caso o usuário não seja superadmin, filtra apenas os cargos da empresa dele.
        $whereClause = null;

        if(!$permissionsModel->user_is_superadmin()) {
            $whereClause = '
                id_empresa = ' . $id_empresa . ' OR is_global = 1
            ';
        }

        $permissionsModel = new PermissoesModel();

        $columns = array(
            [ 'db' => 'nome',  'dt' => 0 ],
            
            [ 'db' => 'descricao',          'dt' => 1 ],

            [ 'db' => 'is_global',    'dt' => 3  ],
            [ 'db' => 'superadmin_only',    'dt' => 4 ],

            [ 'db' => 'id_cargo',         'dt' => 2,
              'formatter' => function($id, $row) use ($permissionsModel) {

                    $id_department = $id;
                    $is_global = $row['is_global'];
                    $html_actions = '';

                    if($permissionsModel->user_has_permission('user.edit') && !$is_global) {
                    $html_actions .= '
                        <button class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Editar" onclick="editDepartment(' . $id_department . ')"><i class="fa fa-edit"></i></button>
                        ';
                    }

                    if($permissionsModel->user_has_permission('user.delete') && !$is_global) {
                        if (!$row['superadmin_only']) {
                            $departmentName = esc($row['nome']);
                            $html_actions .= '
                                <button class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Remover" onclick="deleteDepartment(' . $id_department . ', \''. $departmentName .'\')"><i class="fa fa-trash"></i></button>
                            ';
                        }   
                    }


                  return $html_actions;
              }
            ]
        );

        $result = \SSP::complex($_GET, $db_details, $table, $primaryKey, $columns, $whereClause, null, $joinClause);

        return $this->respond($result);
    }
}
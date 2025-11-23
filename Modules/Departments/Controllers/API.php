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

    protected $permissionsModel;

    public function __construct()
    {
        $this->permissionsModel = new PermissoesModel();
    }

    public function create()
    {
        // Verificar permissões de criar o departamento aqui, se necessário
        if(!$this->permissionsModel->user_has_permission('mod.departments.create') && !$this->permissionsModel->user_is_superadmin()) {
            return redirect()->to('/dashboard/departamentos')->with('department.feedback.success', 'Você não tem permissão para criar um novo departamento.');
        }

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
     * Gera uma descrição concisa para um departamento usando Google Gemini AI.
     * 
     * Esta função recebe o nome de um departamento via JSON no body da requisição
     * e utiliza a API do Google Gemini 2.5-flash para gerar uma descrição profissional
     * com no máximo 40 caracteres.
     * 
     * @return ResponseInterface JSON response com:
     *         - success: { status: 'success', content: string (descrição gerada) }
     *         - error: { status: 'error', message: string, status_code?: int, details?: array }
     * 
     * Request:
     *   POST /dashboard/departamentos/api/generate-description
     *   Content-Type: application/json
     *   Body: { "departmentName": "Nome do Departamento" }
     * 
     * Response Success (200):
     *   { "status": "success", "content": "Descrição Profissional Gerada" }
     * 
     * Response Errors:
     *   - 400: Nome do departamento não fornecido
     *   - 500: Chave de API não carregada ou erro na requisição Gemini
     * 
     * @throws Exception Se houver erro na requisição HTTP ou parsing JSON
     */
    public function generateDescription()
    {
        $json = json_decode($this->request->getBody(), true);
        $departmentName = $json['departmentName'] ?? null;

        if (!$departmentName) {
            return $this->fail('Nome do departamento não fornecido.', 400);
        }

        $client = service('curlrequest');;

        $geminiKey = getenv('GEMINI_API_KEY');

        if (empty($geminiKey)) {
            return $this->fail('Chave de API não carregada.', 500);
        }

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $geminiKey;

        // Payload Corrigido para PHP (será codificado em JSON pelo CI4)
        $payload = [
            // 1. "contents" agora é um ARRAY [ { ... } ]
            'contents' => [ 
                [
                    // É altamente recomendável incluir o "role" mesmo para o primeiro turno
                    'role' => 'user', 
                    'parts' => [
                        [
                            'text' => 'Gere uma descrição concisa e profissional para um departamento chamado '.esc($departmentName).'. A descrição deve ter no máximo 40 caracteres. Responda apenas com a descrição, sem explicações adicionais.Ex: "Departamento de Recursos Humanos".',
                        ],
                    ],
                ],
            ],
        ];

        try {
            $response = $client->request('POST', $url, [
                'json' => $payload,
                'timeout' => 20,
            ]);

            // Obter e decodificar a resposta
            $statusCode = $response->getStatusCode();
            $body = $response->getBody();
            $data = json_decode($body, true);

            if ($statusCode === 200) {
                // Acesso ao texto gerado
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                
                return $this->response->setJSON([
                    'status' => 'success',
                    'description' => $text,
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Erro na API do Gemini',
                    'status_code' => $statusCode,
                    'details' => $data,
                ])->setStatusCode($statusCode);
            }
        }
        catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Exceção ao chamar a API do Gemini: ' . $e->getMessage(),
            ])->setStatusCode(500);
        }
    }

    /**
     * Atualiza um departamento existente.
     */
    public function update($id = null)
    {
        // Tem permissão para atualizar? Se não, redireciona com erro
        if(!$this->permissionsModel->user_has_permission('mod.departments.edit') && !$this->permissionsModel->user_is_superadmin()) {
            return redirect()->to('/dashboard/departamentos')->with('error', 'Você não tem permissão para editar este departamento.');
        }

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
            // Tem permissão para deletar? Se não, retorna erro
            if(!$this->permissionsModel->user_has_permission('mod.departments.delete') && !$this->permissionsModel->user_is_superadmin()) {
                return $this->fail('Você não tem permissão para deletar este departamento.', 403);
            }
            
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
        $whereClause = 'TRUE AND ';

        if(!$permissionsModel->user_is_superadmin()) {
            $whereClause = '
                ( id_empresa = ' . $id_empresa . ' OR is_global = 1)
            ';
        }

        // Adiciona funcionalidade de busca
        if(($_GET['search']['value'] ?? '') !== '') {
            $searchValue = $db->escapeLikeString($_GET['search']['value']);
            $whereClause .= " AND (
                nome LIKE '%" . $searchValue . "%' OR
                descricao LIKE '%" . $searchValue . "%'
            )";
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

                    if(($permissionsModel->user_has_permission('mod.departments.edit') && !$is_global) ||
                        ($permissionsModel->user_is_superadmin() && $is_global)
                ) {
                    $html_actions .= '
                        <button class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Editar" onclick="editDepartment(' . $id_department . ')"><i class="fa fa-edit"></i></button>
                        ';
                    }

                    if(($permissionsModel->user_has_permission('mod.departments.delete') && !$is_global)) {
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
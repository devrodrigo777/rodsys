<?php

namespace Modules\Empresas\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use Modules\Empresas\Services\EmpresasService;

class API extends ResourceController
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
        $this->permissionsModel = new \Modules\Permissoes\Models\PermissoesModel();
    }

    public function create()
    {
        // Se não tiver permissão para criar uma empresa, redireciona com erro
        if(!$this->permissionsModel->user_has_permission('mod.empresas.create') || !$this->permissionsModel->user_is_superadmin()) {
            return redirect()->to('/dashboard/empresas')->with('error', 'Você não tem permissão para criar uma nova empresa.');
        }

        $nome = $_POST['inputRazaoSocial'] ?? null;
        $cnpj = $_POST['inputCnpj'] ?? null;
        $plano_ativo = $_POST['inputPlanoAtivo'] ?? 0;

        $empresasService = new EmpresasService();
        $result = $empresasService->createEmpresa($cnpj, $nome, $plano_ativo);

        if ($result['success']) {
            session()->setFlashdata('success', $result['message']);
            return redirect()->to('dashboard/empresas');
        } else {
            session()->setFlashdata('error', $result['message']);
            return redirect()->back()->withInput();
        }
    }

    public function update($id = null)
    {
        // Se não tiver permissão para editar uma empresa, redireciona com erro
        if(!$this->permissionsModel->user_has_permission('mod.empresas.edit') && !$this->permissionsModel->user_is_superadmin()) {
            return redirect()->to('/dashboard/empresas')->with('error', 'Você não tem permissão para editar esta empresa.');
        }

        if (!$id) {
            session()->setFlashdata('error', 'Empresa não encontrada.');
            return redirect()->to('dashboard/empresas');
        }

        $nome = $_POST['inputRazaoSocial'] ?? null;
        $cnpj = $_POST['inputCnpj'] ?? null;
        $plano_ativo = $_POST['inputPlanoAtivo'] ?? 0;

        $empresasService = new EmpresasService();
        $result = $empresasService->updateEmpresa($id, $cnpj, $nome, $plano_ativo);

        if ($result['success']) {
            session()->setFlashdata('success', $result['message']);
            return redirect()->to('dashboard/empresas');
        } else {
            session()->setFlashdata('error', $result['message']);
            return redirect()->back()->withInput();
        }
    }

    public function delete($id = null)
    {
        // Verificar permissões de deletar a empresa aqui, se necessário
        if(!$this->permissionsModel->user_has_permission('mod.empresas.delete') && !$this->permissionsModel->user_is_superadmin()) {
            return $this->fail('Você não tem permissão para deletar esta empresa.', 403);
        }

        if (!$id) {
            return $this->failNotFound('Empresa não encontrada.');
        }

        $empresasService = new EmpresasService();
        $result = $empresasService->deleteEmpresa($id);

        if ($result['success']) {
            return $this->respond(['success' => true, 'message' => $result['message']]);
        } else {
            return $this->fail($result['message'], 400);
        }
    }

    public function list()
    {
        require_once APPPATH . 'ThirdParty/ssp.class.php';

        $db = db_connect();
        $db_details = [
            'user' => $db->username,
            'pass' => $db->password,
            'db'   => $db->database,
            'host' => $db->hostname,
        ];

        $table = 'empresas';
        $primaryKey = 'id_empresa';

        $columns = array(
            ['db' => 'razao_social', 'dt' => 0],
            ['db' => 'cnpj', 'dt' => 1],
            ['db' => 'plano_ativo', 'dt' => 2, 'formatter' => function($val) {
                return $val ? 'Ativo' : 'Inativo';
            }],
            ['db' => 'data_adesao', 'dt' => 3],
            ['db' => 'id_empresa', 'dt' => 4, 'formatter' => function($id) {
                $html = '';
                $html .= '<button class="btn btn-sm btn-primary" onclick="editEmpresa(' . $id . ')"><i class="fa fa-edit"></i></button> ';
                $html .= '<button class="btn btn-sm btn-danger" onclick="deleteEmpresa(' . $id . ')"><i class="fa fa-trash"></i></button>';
                return $html;
            }]
        );

        $result = \SSP::simple($_GET, $db_details, $table, $primaryKey, $columns);

        return $this->respond($result);
    }
}

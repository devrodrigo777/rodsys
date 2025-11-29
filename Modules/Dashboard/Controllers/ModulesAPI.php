<?php

namespace Modules\Dashboard\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

use Modules\Permissoes\Models\PermissoesModel;


class ModulesAPI extends ResourceController
{
    
    use ResponseTrait;

    private $permissionsModel;

    public function __construct()
    {
        $this->permissionsModel = new PermissoesModel();
    }

    public function list($company_id = null): ResponseInterface
    {
        // Verifica se o usuário tem permissão para visualizar módulos
        if (!$this->permissionsModel->user_has_permission('mod.modules.view') && !$this->permissionsModel->user_is_superadmin()) {
            return $this->failForbidden('Você não tem permissão para visualizar os módulos.');
        }

        $logged_company = session()->get('id_empresa');
        $authorized = false;

        // Antes de iniciarmos, verificaremos se o usuario está tentando ver seus próprios módulos
        if($company_id)
        {
            // a lógica: 
            // se o company_id for diferente do id_empresa do usuário logado
            // e o usuário não for superadmin, bloqueia o acesso
            // usuário está tentando visualizar módulos de outra empresa
            if (($company_id != $logged_company) && !$this->permissionsModel->user_is_superadmin()) {
                return $this->failForbidden('Você não tem permissão para visualizar os módulos de outras empresas.');
            } else {
                $authorized = true;
            }
        }

        require_once APPPATH . 'ThirdParty/ssp.class.php';

        $db = db_connect();
        $db_details = [
            'user' => $db->username,
            'pass' => $db->password,
            'db'   => $db->database,
            'host' => $db->hostname,
        ];

        $joinClause = "LEFT JOIN empresas_modulos em ON modulos.id = em.id_modulo ";
        $isComplex = true;

        if($authorized && $company_id)
        {
            // Filtra os módulos pela empresa específica
            $table = "modulos";

            $joinClause .= "WHERE em.id_empresa = " . intval($company_id);

            
        }
        else
            $table = 'modulos';

        $primaryKey = 'id';

        $columns = array(
            ['db' => 'nome', 'dt' => 0],
            ['db' => 'versao', 'dt' => 1],
            ['db' => 'id_modulo', 'dt' => 3],
            ['db' => 'id', 'dt' => 4],
            ['db' => 'ativo', 'dt' => 2, 'formatter' => function($id, $row) {
                $html = '';

                // echo $id . ' - ' . $row['id_modulo'];
                
                // Verifica se o id do modulo é igual o em.id_modulo e se está ativo
                // Se estiver ativo, ele marca como ativo
                if($row['id'] == $row['id_modulo'])
                    $html .= '<span class="badge bg-success">Ativo</span>';
                else
                    $html .= '<span class="badge bg-secondary">Inativo</span>';

                // if($this->permissionsModel->user_is_superadmin())
                //     $html .= '<button class="btn btn-sm btn-primary" onclick="updateModulo(' . $id . ')"><i class="fa fa-arrow-rotate-right"></i></button> ';

                // $html .= '<button class="btn btn-sm btn-success" onclick="buyModulo(' . $id . ')"><i class="fa fa-bag-shopping"></i></button>';
                return $html;
            }]
        );

        if($isComplex) {
            $result = \SSP::complex($_GET, $db_details, $table, $primaryKey, $columns,null,null, $joinClause);
        } else {
            $result = \SSP::simple($_GET, $db_details, $table, $primaryKey, $columns);
        }

        return $this->respond($result);
    }
}
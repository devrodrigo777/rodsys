<?php

namespace Modules\Empresas\Models;

use CodeIgniter\Model;

class EmpresasModel extends Model
{
    protected $table = 'empresas';
    protected $primaryKey = 'id_empresa';
    protected $allowedFields = ['cnpj', 'razao_social', 'plano_ativo', 'data_adesao'];
    protected $useTimestamps = false;

    public function listForMe()
    {
        $user_id = session()->get('usuario');
        $id_empresa = session()->get('id_empresa');

        // If superadmin, return all companies; otherwise return only the user's company
        $permissionsModel = new \Modules\Permissoes\Models\PermissoesModel();
        if ($permissionsModel->user_is_superadmin()) {
            return $this->findAll();
        }

        return $this->where('id_empresa', $id_empresa)->findAll();
    }

    public function getEmpresaByCnpj($cnpj)
    {
        return $this->where('cnpj', $cnpj)->first();
    }
}

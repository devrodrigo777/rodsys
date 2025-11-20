<?php

namespace Modules\Login\Models;

use CodeIgniter\Model;
use Modules\Permissoes\Models\PermissoesModel;

class EmpresaModel extends Model
{
    protected $table = 'empresas';
    protected $primaryKey = 'id_empresa';
    protected $allowedFields = ['cnpj', 'razao_social', 'data_adesao'];

    /**
     * Esta função irá fazer um findAll em todas as empresas do usuário logado.
     * O critério será a permissão do usuário.
     * @return array
     */
    public function listForMe()
    {
        $permissoes_model = new PermissoesModel();

        if ($permissoes_model->user_has_permission('mod.user.company.listall') || $permissoes_model->user_is_superadmin()) {
            return $this->findAll();
        } elseif ($permissoes_model->user_has_permission('mod.user.company.listme') || $permissoes_model->user_is_superadmin()) {
            // user.company.listme retorna apenas a empresa do usuário logado
            $id_empresa_logada = session()->get('id_empresa');
            return $this->where('id_empresa', $id_empresa_logada)->findAll();
        } else {
            return [];
        }
    }

    public function Me()
    {
        // Retorna apenas a única empresa do usuário logado.
        $id_empresa_logada = session()->get('id_empresa');
        return $this->where('id_empresa', $id_empresa_logada)->findAll();
    }
}

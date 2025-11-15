<?php

namespace Modules\Login\Models;

use CodeIgniter\Model;

class CargosModel extends Model
{
    protected $table = 'cargos';
    protected $primaryKey = 'id_cargo';
    protected $allowedFields = ['nome', 'descricao', 'id_empresa', 'is_global'];

    public function listForMe()
    {
        $id_empresa_logada = session()->get('id_empresa');
        return $this->where('id_empresa', $id_empresa_logada)
                    ->orWhere('is_global', 1)
                    ->findAll();

    }
}

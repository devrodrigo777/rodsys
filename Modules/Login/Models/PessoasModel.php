<?php

namespace Modules\Login\Models;

use CodeIgniter\Model;

class PessoasModel extends Model
{
    protected $table            = 'pessoas';
    protected $primaryKey       = 'id_pessoa';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_pessoa',
        'id_empresa',
        'id_usuario_login',
        'nome_completo',
        'id_cargo',
    ];
}

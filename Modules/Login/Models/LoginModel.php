<?php

namespace Modules\Login\Models;

use CodeIgniter\Model;

class LoginModel extends Model
{
    protected $table = 'login';
    protected $primaryKey = 'id_usuario';
    protected $allowedFields = ['id_usuario', 'id_empresa', 'usuario', 'senha_hash', 'ativo'];

    // Teremos created_at e updated_at automáticos
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

}
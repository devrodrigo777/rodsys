<?php

namespace Modules\Departments\Models;

use CodeIgniter\Model;

class DepartmentModel extends Model
{
    protected $table = 'cargos';
    protected $primaryKey = 'id_cargo';
    protected $allowedFields = ['id_empresa', 'nome', 'descricao', 'superadmin_only', 'is_global'];
}

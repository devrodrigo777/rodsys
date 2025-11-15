<?php

namespace App\Models;

use CodeIgniter\Model;

class TemaModel extends Model
{
    // Model properties and methods go here
    protected $table = 'temas';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nome', 
        'diretorio', 
        'descricao', 
        'ativo'
    ];

    protected $returnType = 'array';

    protected $useAutoIncrement = true;
}
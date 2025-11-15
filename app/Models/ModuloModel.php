<?php

namespace App\Models;

use CodeIgniter\Model;

class ModuloModel extends Model
{
    // Model properties and methods go here
    protected $table = 'modulos';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nome', 
        'namespace', 
        'diretorio', 
        'versao', 
        'ativo', 
        'configuracoes'
    ];

    protected $returnType = 'array';

    protected $useAutoIncrement = true;

    // Metodo para listar os módulos ativos
    public function listarModulosAtivos()
    {
        return $this->where('ativo', 1)->orderBy('order', 'ASC')->findAll();
    }
}
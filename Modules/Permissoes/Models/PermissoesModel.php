<?php

namespace Modules\Permissoes\Models;

use CodeIgniter\Model;

class PermissoesModel extends Model
{
    protected $table = 'permissoes';
    protected $primaryKey = 'id_permissao';
    protected $allowedFields = ['slug', 'descricao', 'cliente_configuravel', 'is_superadmin'];

    protected $current_permissions = [];

    public function __construct()
    {
        // carregar previamente todas as permissoes do usuario logado
        $this->current_permissions = $this->listMyPermissions();


        parent::__construct();
    }

    public function getPermissoesBySlug($slug)
    {
        return $this->where('slug', $slug)->first();
    }

    public function getAllPermissions()
    {
        //Obter todas as permissões where superadmin = 0 CASO o usuario logado NÃO SEJA superadmin.
        if(!$this->user_is_superadmin()) {
            return $this->select('id_permissao, slug, descricao')->where('is_superadmin', 0)->findAll();
        } else {
            return $this->select('id_permissao, slug, descricao')->findAll();
        }
    }

    public function listMyPermissions()
    {
        $user_id = session()->get('usuario');
        $id_empresa = session()->get('id_empresa');

        // Implement your logic to retrieve the permissions for the logged-in user
        // This is a placeholder implementation and should be replaced with actual logic
        $db = \Config\Database::connect();
        $builder = $db->table('cargos_permissoes')->select('permissoes.slug, permissoes.descricao');
        $builder->join('permissoes', 'permissoes.id_permissao = cargos_permissoes.id_permissao');
        $builder->join('pessoas', 'pessoas.id_cargo = cargos_permissoes.id_cargo');
        $builder->where('pessoas.id_usuario_login', $user_id);
        $result = $builder->get();

        return $result->getResultArray();
    }

    public function user_has_permission($permission_slug)
    {
        foreach ($this->current_permissions as $permission) {
            if ($permission['slug'] === $permission_slug) {
                return true;
            }
        }
        return false;
    }

    public function user_is_superadmin()
    {
        foreach ($this->current_permissions as $permission) {
            if ($permission['slug'] === 'superadmin') {
                return true;
            }
        }
        return false;
    }
}

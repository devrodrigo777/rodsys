<?php

namespace Modules\Empresas\Libraries;

use App\Libraries\ModulesMenu;
use Modules\Permissoes\Models\PermissoesModel;

class Menu extends ModulesMenu
{
    public function __construct()
    {

    }

    public function Sidebar_Menu()
    {
        $menu = new ModulesMenu();

        $permissoesModel = new PermissoesModel();

        // Verifica se o usuário tem permissão para visualizar empresas
        if ($permissoesModel->user_has_permission('mod.empresas.view')  || $permissoesModel->user_is_superadmin()) {
            $menu->addMenuItem('Empresas', 'dashboard/empresas', 'fa-solid fa-building', null, 'dashboard/empresas/*');
        }

        return $menu;
    }
}

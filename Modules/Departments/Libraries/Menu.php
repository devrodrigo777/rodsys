<?php

namespace Modules\Departments\Libraries;

use App\Libraries\ModulesMenu;
use Modules\Permissoes\Models\PermissoesModel;



class Menu extends ModulesMenu
{
    public function __construct()
    {
        
    }

    public function Sidebar_Menu()
    {
        // Here we will set the array variables to build the sidebar menu
        $login_menu = new ModulesMenu();

        $permissoesModel = new PermissoesModel();
        
        // Verifica se o usuário tem permissão para visualizar departamentos
        if ($permissoesModel->user_has_permission('mod.departments.view') || $permissoesModel->user_is_superadmin()) {
            $login_menu->addMenuItem('Departamentos', 'dashboard/departamentos', 'fa-solid fa-building-user', null, 'dashboard/departamentos/*');
        }
        
        return $login_menu;
    }
}

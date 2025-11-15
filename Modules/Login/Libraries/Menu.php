<?php

namespace Modules\Login\Libraries;

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

        // Verifica se o usuário tem permissão para visualizar usuários
        if ($permissoesModel->user_has_permission('user.view')  || $permissoesModel->user_is_superadmin()) {
            $login_menu->addMenuItem('Acessos', 'acessos', 'fa-solid fa-key', null, 'dashboard/acessos/*');
            $login_menu->addMenuItem('Usuários', 'dashboard/acessos/usuarios', 'fa-solid fa-users', 'Acessos');
        }
        
        return $login_menu;
    }
}

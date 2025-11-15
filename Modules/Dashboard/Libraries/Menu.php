<?php

namespace Modules\Dashboard\Libraries;

use App\Libraries\ModulesMenu;



class Menu extends ModulesMenu
{
    public function __construct()
    {
        
    }

    public function Sidebar_Menu()
    {
        // Here we will set the array variables to build the sidebar menu
        $dashboard_menu = new ModulesMenu();

        $dashboard_menu->addMenuHeader('Principal');

        $dashboard_menu->addMenuItem('Painel', 'dashboard', 'fa-solid fa-table-columns');

        
        return $dashboard_menu;
    }
}

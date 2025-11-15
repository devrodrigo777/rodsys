<?php

use Modules\Dashboard\Libraries\Menuactive;

$first_effect = true;
$menu_foreach_id = 0;

foreach ($modules as $module):

    $menu_foreach_id++;

    if($first_effect) $first_effect = false;
    else {
        // Separador depois do primeiro módulo
        echo '<div style="border-bottom: 1px solid #e0e0e0; margin: 10px 0;"></div>';
    }

    // get module menu library
    $module_menu_class = "\\Modules\\" . $module['diretorio'] . "\\Libraries\\Menu";
    if (class_exists($module_menu_class)) {
        $module_menu_instance = new $module_menu_class();
        $menu_array = $module_menu_instance->Sidebar_Menu()->getMenuArray();
    } else {
        $menu_array = [];
    }

    if(empty($menu_array)){
        $first_effect = true;
        continue;
    }

    // Sort the menu_array by the 'order' key
    usort($menu_array, function ($a, $b) {
        return ($a['order'] ?? 0) <=> ($b['order'] ?? 0);
    });
    foreach ($menu_array as $menu):
        // check if is current page (for active class)
        
        $has_submenu = isset($menu['children']) && count($menu['children']) > 0;
        $is_active = Menuactive::is_active_menu(uri_string(), $menu['active_pattern']);
    ?>

        <?php
        // verificaremos se o menu possui submenus. Se possuir, criaremos a estrutura de submenu
        if ($has_submenu) {
            // Verifica se algum dos submenus está ativo
            $is_any_child_active = false;
            foreach ($menu['children'] as $child) {
                if (Menuactive::is_active_menu(uri_string(), $child['active_pattern'])) {
                    $is_any_child_active = true;
                    break;
                }
            }
        } else {
            $is_any_child_active = false;
        }
        ?>

        <?php if (isset($menu['is_header']) && $menu['is_header'] === true): ?>
            <div class="sidenav-menu-heading"><?= $menu['nome'] ?></div>
        <?php else: ?>
            <?php if($has_submenu): ?>
            <a class="nav-link collapsed <?= (Menuactive::is_active_menu(uri_string(), $menu['active_pattern'])) ? 'active' : '' ?>" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapse<?=$menu_foreach_id?>" aria-expanded="false" aria-controls="collapse<?=$menu_foreach_id?>">
                <div class="nav-link-icon"><i class="<?= $menu['icone'] ?>"></i></div>
                    <?=$menu['nome']?>
                <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
            </a>
            <div class="collapse" id="collapse<?=$menu_foreach_id?>" data-bs-parent="#accordionSidenav">
                
                <nav class="sidenav-menu-nested nav">
                    <?php foreach ($menu['children'] as $child): ?>
                        <a class="nav-link <?= (Menuactive::is_active_menu(uri_string(), $child['active_pattern'])) ? 'active' : '' ?>"
                            href="<?= base_url($child['url']) ?>">
                            <?= $child['nome'] ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
            </div>
            <?php else: ?>
            <a class="nav-link <?=($is_active) ? 'active' : ''?>" href="<?= base_url($menu['url']) ?>">
                <div class="nav-link-icon"><i class="<?= $menu['icone'] ?>"></i></div>
                <?= $menu['nome'] ?>
            </a>
            <?php endif; ?>
        <?php endif; ?>
    <?php endforeach; ?>

    
<?php endforeach; ?>
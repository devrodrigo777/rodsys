<?php

/**
 * This is the ModulesMenu library which can be extended by other modules to create their own menu structures.
 */

namespace App\Libraries;

class ModulesMenu
{
    protected $menu = [];

    public function __construct()
    {
        // Constructor can be used to initialize common menu properties or load configurations
    }

    public function getMenuArray(): array
    {
        return $this->menu;
    }

    protected function addMenuHeader(string $title)
    {
        $this->menu[] = [
            'nome' => $title,
            'url' => '',
            'icone' => '',
            'children' => [],
            'is_header' => true,
            'active_pattern' => '',
        ];
    }

    /**
     * Adds a menu item to the menu structure.
     *
     * @param string $title The title of the menu item.
     * @param string $url The URL the menu item links to.
     * @param string $icon The icon class for the menu item (e.g., 'fas fa-home').
     * @param string|null $parent The title of the parent menu item, if it's a submenu item.
     * @return void
     */
    protected function addMenuItem(string $title, string $url, string $icon, ?string $parent = null, string $active_pattern = ''): void
    {
        $item = [
            'nome' => $title,
            'url' => $url,
            'icone' => $icon,
            'children' => [],
            'active_pattern' => $active_pattern,
        ];

        if ($active_pattern === '') {
            $item['active_pattern'] = $url;
        }

        if ($parent === null) {
            $this->menu[] = $item;
        } else {
            $this->addMenuItemToParent($this->menu, $parent, $item);
        }
    }

    /**
     * Recursively adds a menu item to a specified parent.
     *
     * @param array $menu The current menu array to search.
     * @param string $parentTitle The title of the parent menu item.
     * @param array $item The menu item to add.
     * @return bool True if the item was added, false otherwise.
     */
    private function addMenuItemToParent(array &$menu, string $parentTitle, array $item): bool
    {
        foreach ($menu as &$menuItem) {
            if ($menuItem['nome'] === $parentTitle) {
                $menuItem['children'][] = $item;
                return true;
            }
            if (!empty($menuItem['children'])) {
                if ($this->addMenuItemToParent($menuItem['children'], $parentTitle, $item)) {
                    return true;
                }
            }
        }

        return false;
    }
}
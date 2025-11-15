<?php

// app/Services/ModuleService.php

namespace App\Services;

use App\Models\ModuloModel;
use App\Models\TemaModel;
use Codeigniter\Config\Services;

class ModuleService
{
    protected $moduloModel;
    protected $temaModel;

    protected $activeModels = [];
    protected $activeTheme;


    public function __construct()
    {
        $this->moduloModel = new ModuloModel();
        $this->temaModel = new TemaModel();

        $this->loadConfigurations();
    }

    protected function loadConfigurations()
    {
        // Load active modules
        $modules = $this->moduloModel->where('ativo', 1)->findAll();
        foreach ($modules as $module) {
            $this->activeModels[] = $module;
        }

        // Load active theme
        $theme = $this->temaModel->where('ativo', 1)->first();
        if ($theme) {
            $this->activeTheme = $theme;
        }
    }

    public function getActiveModules()
    {
        return $this->activeModels;
    }

    public function getActiveThemePath()
    {
        if ($this->activeTheme) {
            return ROOTPATH . 'Themes/' . $this->activeTheme['diretorio'] . '/Views/';
        }
        return APPPATH . 'Views/'; // Fallback para a view padrão
    }

    public function getViewThemePath()
    {
        if ($this->activeTheme) {
            return 'Modules/' . $this->activeTheme['diretorio'] . '/Views/';
        }
        return APPPATH . 'Views/'; // Fallback para a view padrão
    }
}
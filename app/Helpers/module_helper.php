<?php

if(!function_exists('module_view'))
{
    function module_view($module, $view, $data = [], $options = [])
    {
        $moduleService = \Config\Services::moduleService();

        
        // Cria uma instância do Renderer com o caminho do tema ativo
        return view('Modules\\'. $module . '\Views\\' . $view, $data, $options);
    }
}
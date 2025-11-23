<?php

namespace Modules\Dashboard\Services;

use Modules\Empresas\Models\EmpresasModel;

class ModulesService
{
    private $renderFunction;
    private $params;

    public function __construct($renderFunction = 'dashboard', $params = null)
    {
        $this->renderFunction = $renderFunction;
        $this->params = $params;
    }

    public function render($data = [])
    {
        $func = $this->renderFunction;
        return $this->$func($data);
    }

    public function dashboard($data = [])
    {
        return module_view('Dashboard', 'Dashboard/Modules', $data);
    }

    public function forCompanies($data = [])
    {
        $empresaModel = new EmpresasModel();

        $data['company_id'] = $this->params;
        $data['company_info'] = $empresaModel->where('id_empresa', $this->params)->first();

        return module_view('Dashboard', 'Dashboard/ModulesForCompanies', $data);
    }
}
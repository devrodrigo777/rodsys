<?php

namespace Modules\Dashboard\Services;

class MainDashboard
{
    public function render($data = [])
    {
        return module_view('Dashboard', 'Dashboard/Main', $data);
    }
}

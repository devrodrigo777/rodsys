<?php

use CodeIgniter\Router\RouteCollection;

$routes->get('dashboard', '\Modules\Dashboard\Controllers\Dashboard::index');
$routes->get('dashboard/modulos', '\Modules\Dashboard\Controllers\Modules::index');
$routes->get('dashboard/modulos/(:num)', '\Modules\Dashboard\Controllers\Modules::forCompanies/$1');

$routes->get('dashboard/modules/api/list', '\Modules\Dashboard\Controllers\ModulesAPI::list');
$routes->get('dashboard/modules/api/list/(:num)', '\Modules\Dashboard\Controllers\ModulesAPI::list/$1');
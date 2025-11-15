<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Abaixo estabeleceremos routes para API
$routes->post('api/create-user', 'Api::createUser');



// Carregar os routes dinamicamente pelos modulos
$moduleService = service("moduleService");
$activeModules = $moduleService->getActiveModules();

foreach ($activeModules as $module) {
    $moduleDir = ROOTPATH . 'Modules/' . $module['diretorio'];
    
    $routeFile = $moduleDir . '/Config/Routes.php';

    if (file_exists($routeFile)) {
        // Usa o namespace definido no BD (Ex: Modules\Blog)
        $routes->group('/', ['namespace' => $module['namespace']], function($routes) use ($routeFile) {
            require $routeFile;
        });
    }
}

// $routes->group('/login', function($routes) {
//     if(file_exists(ROOTPATH . 'Modules/Login/Config/Routes.php')) {
//         require ROOTPATH . 'Modules/Login/Config/Routes.php';
//     }
// });

<?php

use CodeIgniter\Router\RouteCollection;

/**
 * Verifica permissão antes mesmo de registrar as rotas
 */
$permissoesModel = new \Modules\Permissoes\Models\PermissoesModel();

if (! $permissoesModel->user_has_permission('mod.empresas.view')
    && ! $permissoesModel->user_is_superadmin()) {
    $forbiddenPermissionCallback = function(){
        $session = session();
        $session->setFlashdata('swal.feedback', ['message' => 'Você não tem permissão para acessar o módulo de Empresas.']);
        return redirect()->to(base_url('dashboard'));
    };

    $routes->group('dashboard/empresas', function($routes) use ($forbiddenPermissionCallback) {
        $routes->match(['get', 'post', 'put', 'delete'], '/', $forbiddenPermissionCallback);
        $routes->match(['get', 'post', 'put', 'delete'], '/(:any)', $forbiddenPermissionCallback);
    });

     $routes->group('empresas/api', function($routes) use ($forbiddenPermissionCallback) {
        $routes->match(['get', 'post', 'put', 'delete'], '/', $forbiddenPermissionCallback);
        $routes->match(['get', 'post', 'put', 'delete'], '/(:any)', $forbiddenPermissionCallback);
    });
}

/**
 * @var $routes
 */
$routes->get('dashboard/empresas', '\Modules\Empresas\Controllers\Empresas::index');
$routes->get('dashboard/empresas/(:num)', '\Modules\Empresas\Controllers\Empresas::editar/$1');
$routes->get('dashboard/empresas/novo', '\Modules\Empresas\Controllers\Empresas::novo');

$routes->post('empresas/api/create', '\Modules\Empresas\Controllers\API::create');
$routes->post('empresas/api/update/(:num)', '\Modules\Empresas\Controllers\API::update/$1');
$routes->delete('empresas/api/delete/(:num)', '\Modules\Empresas\Controllers\API::delete/$1');

$routes->get('empresas/api/list', '\Modules\Empresas\Controllers\API::list');

<?php

use CodeIgniter\Router\RouteCollection;


/**
 * @var $routes
 */

/**
 * Verifica permissão antes mesmo de registrar as rotas
 */
$permissoesModel = new \Modules\Permissoes\Models\PermissoesModel();

if (! $permissoesModel->user_has_permission('departments.view')
    && ! $permissoesModel->user_is_superadmin()) {
    $forbiddenPermissionCallback = function(){
        $session = session();
        $session->setFlashdata('swal.feedback', ['message' => 'Você não tem permissão para acessar o módulo de Departamentos.']);
        return redirect()->to(base_url('dashboard'));
    };

    $routes->group('dashboard/departamentos', function($routes) use ($forbiddenPermissionCallback) {
        $routes->match(['get', 'post', 'put', 'delete'], '/', $forbiddenPermissionCallback);
        $routes->match(['get', 'post', 'put', 'delete'], '/(:any)', $forbiddenPermissionCallback);
    });
}

$routes->get('dashboard/departamentos', '\Modules\Departments\Controllers\Departments::index');
$routes->get('dashboard/departamentos/(:num)', '\Modules\Departments\Controllers\Departments::edit/$1');
$routes->get('dashboard/departamentos/novo', '\Modules\Departments\Controllers\Departments::novo');

$routes->post('dashboard/departamentos/novo', '\Modules\Departments\Controllers\API::create');
$routes->post('dashboard/departamentos/(:num)', '\Modules\Departments\Controllers\API::update/$1');
$routes->delete('dashboard/departamentos/(:num)', '\Modules\Departments\Controllers\API::delete/$1');

$routes->get('dashboard/departamentos/api/read', '\Modules\Departments\Controllers\API::list');


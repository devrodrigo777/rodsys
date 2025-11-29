<?php

use CodeIgniter\Router\RouteCollection;

/**
 * Verifica permissão antes mesmo de registrar as rotas
 */
$permissoesModel = new \Modules\Permissoes\Models\PermissoesModel();

if (! $permissoesModel->user_has_permission('mod.user.view')
    && ! $permissoesModel->user_is_superadmin()) {
    $forbiddenPermissionCallback = function(){
        $session = session();
        $session->setFlashdata('swal.feedback', ['message' => 'Você não tem permissão para acessar o módulo de Acessos.']);
        return redirect()->to(base_url('dashboard'));
    };

    $routes->group('dashboard/acessos', function($routes) use ($forbiddenPermissionCallback) {
        $routes->match(['get', 'post', 'put', 'delete'], '/', $forbiddenPermissionCallback);
        $routes->match(['get', 'post', 'put', 'delete'], '(:any)', $forbiddenPermissionCallback);
    });
}

$routes->get('dashboard/acessos/usuarios', '\Modules\Login\Controllers\Login::manage');
$routes->get('dashboard/acessos/usuarios/(:num)', '\Modules\Login\Controllers\Login::edit/$1');
$routes->post('dashboard/acessos/usuarios/(:num)', '\Modules\Login\Controllers\LoginAPI::update/$1');

$routes->get('dashboard/acessos/usuarios/criar', '\Modules\Login\Controllers\Login::newUser');
$routes->get('dashboard/acessos/usuarios/api/list', '\Modules\Login\Controllers\LoginAPI::userList');

$routes->get('login', '\Modules\Login\Controllers\Login::index');
$routes->get('login/out', '\Modules\Login\Controllers\Login::logout');

$routes->post('login/authenticate', '\Modules\Login\Controllers\Login::authenticate');

$routes->post('login/api/create-user', '\Modules\Login\Controllers\LoginAPI::createUser');
$routes->post('login/api/create-company', '\Modules\Login\Controllers\LoginAPI::createCompany');
$routes->post('login/api/update-user', '\Modules\Login\Controllers\LoginAPI::updateUser');
$routes->delete('login/api/delete-user/(:num)', '\Modules\Login\Controllers\LoginAPI::deleteUser/$1');
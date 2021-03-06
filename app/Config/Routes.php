<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override('\App\Controllers\Controller::page_not_found');
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Dashboard::index');

// requests to be sent to the Auth Login Method
$routes->get('login', 'Auth::login');
$routes->get('lockscreen', 'Auth::login');
$routes->get('auth/cronjob', 'Auth::unknown_page');

// set some routes which should return false
$routes->get('splash', 'Dashboard::splash');

// save information
$routes->get('controller/quicksave', 'Controller::page_not_found');
$routes->get('controller/delete', 'Controller::page_not_found');
$routes->get('comments/save', 'Controller::page_not_found');
$routes->get('users/save', 'Controller::page_not_found');
$routes->get('media/upload', 'Controller::page_not_found');

// api routing
$routes->match(['put', 'delete', 'get', 'post'], '/api(:any)', 'Api::index/$1/$2/$3');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}

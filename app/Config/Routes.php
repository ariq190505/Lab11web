<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setAutoRoute(true);

$routes->get('/', 'Home::index');
$routes->get('/about', 'Page::about');
$routes->get('/contact', 'Page::contact');
$routes->get('/faqs', 'Page::faqs');
$routes->get('/artikel', 'Artikel::index');
$routes->get('/artikel/(:any)', 'Artikel::view/$1');
$routes->get('/testdb', 'Page::testdb');
$routes->get('/createdb', 'Page::createdb');

// Auth routes
$routes->group('auth', function($routes) {
    $routes->add('login', 'Auth::login');
    $routes->add('register', 'Auth::register');
    $routes->get('logout', 'Auth::logout');
});

// User routes (untuk compatibility dengan filter)
$routes->group('user', function($routes) {
    $routes->add('login', 'User::login');
    $routes->add('register', 'User::register');
    $routes->get('logout', 'User::logout');
});


// Debug routes (temporary)
$routes->get('dbtest/test', 'DbTest::test');
$routes->get('sessiondebug/test', 'SessionDebug::test');
$routes->get('sessiondebug/forceLogin', 'SessionDebug::forceLogin');
$routes->get('sessiondebug/clear', 'SessionDebug::clear');

// Simple Login (no database required)
$routes->add('simplelogin', 'SimpleLogin::index');
$routes->get('simplelogout', 'SimpleLogin::logout');

// Dummy Data Generator
$routes->get('dummydata/createImages', 'DummyData::createImages');
$routes->get('dummydata/createArticles', 'DummyData::createArticles');

// AJAX routes
$routes->group('ajax', function($routes) {
    $routes->get('/', 'AjaxController::index');
    $routes->get('artikel', 'AjaxController::artikel');
    $routes->get('simple', 'AjaxController::simple');
    $routes->get('getData', 'AjaxController::getData');
    $routes->get('getAllData', 'AjaxController::getAllData');
    $routes->get('getDataRange/(:num)/(:num)', 'AjaxController::getDataRange/$1/$2');
    $routes->get('getById/(:num)', 'AjaxController::getById/$1');
    $routes->post('delete/(:num)', 'AjaxController::delete/$1');
    $routes->post('create', 'AjaxController::create');
    $routes->post('update/(:num)', 'AjaxController::update/$1');
});

// Admin routes
$routes->group('admin', function($routes) {
    $routes->get('artikel', 'Artikel::admin_index');
    $routes->add('artikel/add', 'Artikel::add');
    $routes->add('artikel/edit/(:any)', 'Artikel::edit/$1');
    $routes->get('artikel/delete/(:any)', 'Artikel::delete/$1');
});

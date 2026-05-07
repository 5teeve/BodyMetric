<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
	require SYSTEMPATH . 'Config/Routes.php';
}

// Router Setup
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

// Application Routes
$routes->get('/', 'Home::index');
$routes->get('welcome', 'Home::index');

// Routes to render view files directly
$routes->get('profil', static function () {
	return view('profil/profil');
});

$routes->get('inscription/step1', static function () {
	return view('inscription/register_step1');
});

$routes->get('inscription/step2', static function () {
	return view('inscription/register_step2');
});


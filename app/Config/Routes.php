<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AuthController::showStep1');
$routes->get('/', 'ProfilController::index');

$routes->get('/connexion', 'AuthController::showLogin');
$routes->post('/connexion', 'AuthController::handleLogin');
$routes->get('/logout', 'AuthController::logout');

$routes->get('/objectif', 'ObjectifController::index');
// API endpoint for objectives distribution (Chart.js)
$routes->get('/api/objectifs/distribution', 'ObjectifController::distribution');

$routes->get('/inscription/step1', 'AuthController::showStep1');
$routes->post('/inscription/step1', 'AuthController::handleStep1');
$routes->get('/inscription/step2', 'AuthController::showStep2');
$routes->post('/inscription/step2', 'AuthController::handleStep2');

$routes->get('/ajax/calculate-imc', 'AuthController::calculateImcAjax');
$routes->get('/portefeuille', 'WalletController::index');
$routes->post('/ajax/portefeuille/valider-code', 'WalletController::validateCodeAjax');

$routes->get('/profil', 'ProfilController::index');
$routes->post('/profil/perso-ajax', 'ProfilController::updatePersonal');
$routes->post('/profil/sante-ajax', 'ProfilController::updateHealth');

$routes->get('/bo', 'Bo\DashboardController::index');
$routes->get('/bo/dashboard', 'Bo\DashboardController::index');
$routes->get('/bo/codes', 'Bo\CodeController::index');
$routes->get('/bo/codes/form', 'Bo\CodeController::form');
$routes->get('/bo/codes/form/(:num)', 'Bo\CodeController::form/$1');
$routes->post('/bo/codes/form', 'Bo\CodeController::store');
$routes->post('/bo/codes/update/(:num)', 'Bo\CodeController::update/$1');
$routes->post('/bo/codes/invalidate/(:num)', 'Bo\CodeController::invalidate/$1');
$routes->post('/bo/codes/delete/(:num)', 'Bo\CodeController::delete/$1');

$routes->get('/export-pdf', 'ExportPdfController::generate');

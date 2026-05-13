<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Route racine : redirige vers profil si connecté, sinon connexion
$routes->get('/', 'Home::index');

$routes->get('/connexion', 'AuthController::showLogin');
$routes->post('/connexion', 'AuthController::handleLogin');
$routes->get('/logout', 'AuthController::logout');

$routes->get('/objectif', 'ObjectifController::index');
$routes->post('/objectif/store', 'ObjectifController::store');
$routes->get('/resultat', 'ResultatsController::index');
$routes->get('/resultats', 'ResultatsController::index');
$routes->post('/resultats', 'ResultatsController::index');

// Régimes - Utilisation front-end
$routes->get('/mes-regimes', 'RegimesController::myRegimes');
$routes->post('/regimes/choisir', 'RegimesController::choisir');
$routes->post('/regimes/choisir-combo', 'RegimesController::choisirCombo');
$routes->post('/regimes/cancel/(:num)', 'RegimesController::cancel/$1');
$routes->get('/regimes/detail/(:num)', 'RegimesController::detail/$1');

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
$routes->post('/profil/gold-ajax', 'ProfilController::upgradeGold');

$routes->get('/gold', 'ProfilController::showGold');

$routes->get('/suggestions', 'SuggestionController::view');
$routes->get('/suggestions/api', 'SuggestionController::index');
$routes->get('/suggestions/type/(:alpha)', 'SuggestionController::getActivitesByType/$1');
$routes->get('/suggestions/intensite/(:alpha)', 'SuggestionController::getActivitesByIntensite/$1');

$routes->get('/bo', 'Bo\DashboardController::index');
$routes->get('/bo/dashboard', 'Bo\DashboardController::index');
$routes->get('/bo/codes', 'Bo\CodeController::index');
$routes->get('/bo/codes/form', 'Bo\CodeController::form');
$routes->get('/bo/codes/form/(:num)', 'Bo\CodeController::form/$1');
$routes->post('/bo/codes/form', 'Bo\CodeController::store');
$routes->post('/bo/codes/update/(:num)', 'Bo\CodeController::update/$1');
$routes->post('/bo/codes/invalidate/(:num)', 'Bo\CodeController::invalidate/$1');
$routes->post('/bo/codes/delete/(:num)', 'Bo\CodeController::delete/$1');

// CRUD Régimes (Back-office)
$routes->get('/bo/regimes', 'Bo\RegimeController::index');
$routes->get('/bo/regimes/form', 'Bo\RegimeController::form');
$routes->get('/bo/regimes/form/(:num)', 'Bo\RegimeController::form/$1');
$routes->post('/bo/regimes/form', 'Bo\RegimeController::store');
$routes->post('/bo/regimes/update/(:num)', 'Bo\RegimeController::update/$1');
$routes->post('/bo/regimes/delete/(:num)', 'Bo\RegimeController::delete/$1');

// CRUD Activités (Back-office)
$routes->get('/bo/activites', 'Bo\ActiviteController::index');
$routes->get('/bo/activites/form', 'Bo\ActiviteController::form');
$routes->get('/bo/activites/form/(:num)', 'Bo\ActiviteController::form/$1');
$routes->post('/bo/activites/form', 'Bo\ActiviteController::store');
$routes->post('/bo/activites/update/(:num)', 'Bo\ActiviteController::update/$1');
$routes->post('/bo/activites/delete/(:num)', 'Bo\ActiviteController::delete/$1');

// CRUD Paramètres (Back-office)
$routes->get('/bo/parametres', 'Bo\ParametreController::index');
$routes->post('/bo/parametres/update', 'Bo\ParametreController::update');

$routes->get('/export-pdf', 'ExportPdfController::generate');
$routes->get('/export-pdf/(:num)', 'ExportPdfController::generate/$1');

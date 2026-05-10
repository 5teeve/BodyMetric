<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AuthController::showStep1');

$routes->get('/inscription/step1', 'AuthController::showStep1');
$routes->post('/inscription/step1', 'AuthController::handleStep1');
$routes->get('/inscription/step2', 'AuthController::showStep2');
$routes->post('/inscription/step2', 'AuthController::handleStep2');
$routes->get('/ajax/calculate-imc', 'AuthController::calculateImcAjax');

$routes->get('/profil', 'ProfilController::index');
$routes->post('/profil/perso-ajax', 'ProfilController::updatePersonal');
$routes->post('/profil/sante-ajax', 'ProfilController::updateHealth');

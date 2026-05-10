<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

<<<<<<< Updated upstream
=======
$routes->get('/connexion', 'AuthController::showLogin');
$routes->post('/connexion', 'AuthController::handleLogin');
$routes->get('/logout', 'AuthController::logout');

$routes->get('/objectif', 'ObjectifController::index');

>>>>>>> Stashed changes
$routes->get('/inscription/step1', 'AuthController::showStep1');
$routes->post('/inscription/step1', 'AuthController::handleStep1');
$routes->get('/inscription/step2', 'AuthController::showStep2');
$routes->post('/inscription/step2', 'AuthController::handleStep2');
$routes->get('/ajax/calculate-imc', 'AuthController::calculateImcAjax');

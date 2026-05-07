<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Registration routes
$routes->get('/inscription/step1', 'AuthController::showStep1');
$routes->post('/inscription/step1', 'AuthController::handleStep1');
$routes->get('/inscription/step2', 'AuthController::showStep2');
$routes->post('/inscription/step2', 'AuthController::handleStep2');

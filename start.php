<?php

use Goteo\Application\App;
use Goteo\Application\Config;
use Symfony\Component\Routing\Route;

// Autoload additional Classes
Config::addAutoloadDir(__DIR__ . '/src');

// Override auth routes (no prefix)
$routes = App::getRoutes();

$routes->get('auth-login')->setDefaults(array(
    '_controller' => 'Goteo\Controller\AuthController\CAS::casLoginAction',
));

$routes->get('auth-login-old-route')->setDefaults(array(
    '_controller' => 'Goteo\Controller\AuthController\CAS::casLoginAction',
));

$routes->get('auth-logout')->setDefaults(array(
    '_controller' => 'Goteo\Controller\AuthController\CAS::casLogoutAction',
));

$routes->get('auth-logout-old-route')->setDefaults(array(
    '_controller' => 'Goteo\Controller\AuthController\CAS::casLogoutAction',
));

// Needed by Goteo\Controller\PoolController::validate
$routes->get('auth-signup')->setDefaults(array(
    '_controller' => 'Goteo\Controller\AuthController\CAS::casSignupAction',
));

// Hidden, only to access as local user (e.g. root)
$routes->add('auth-local-login', new Route('/local-login', array(
    '_controller' => 'Goteo\Controller\AuthController::loginAction',
)));

// Hidden, only to access as local user (e.g. root)
$routes->add('auth-local-logout', new Route('/local-logout', array(
	'_controller' => 'Goteo\Controller\AuthController::logoutAction'
)));

// Remove remaining routes defined in src/Routes/auth_routes.php
$routes->remove('outh-login');
$routes->remove('auth-signup-old-route');
$routes->remove('auth-oauth-signup');
$routes->remove('auth-oauth-signup-old-route');
$routes->remove('auth-password-recovery');
$routes->remove('auth-password-reset');

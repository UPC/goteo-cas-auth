<?php

use Goteo\Application\App;
use Goteo\Application\Config;
use Symfony\Component\Routing\Route;

// Autoload additional Classes
Config::addAutoloadDir(__DIR__ . '/src');

// Override auth routes (no prefix)
$routes = App::getRoutes();

$routes->add('auth-cas-login', new Route('/login', array(
    '_controller' => 'Goteo\Controller\AuthController\CAS::casLoginAction',
)));

$routes->add('auth-cas-logout', new Route('/logout', array(
    '_controller' => 'Goteo\Controller\AuthController\CAS::casLogoutAction',
)));

// Hidden, only to access as local user (e.g. root)
$routes->add('auth-local-login', new Route('/local-login', array(
    '_controller' => 'Goteo\Controller\AuthController::loginAction',
)));

// Hidden, only to access as local user (e.g. root)
$routes->add('auth-local-logout', new Route('/local-logout', array(
	'_controller' => 'Goteo\Controller\AuthController::logoutAction'
)));

// Needed by Goteo\Controller\PoolController::validate
$routes->add('auth-cas-signup', new Route('/signup', array(
    '_controller' => 'Goteo\Controller\AuthController\CAS::casSignupAction',
)));

$routes->remove('outh-login');
$routes->remove('auth-signup');
$routes->remove('auth-signup-old-route');
$routes->remove('auth-oauth-signup');
$routes->remove('auth-oauth-signup-old-route');
$routes->remove('auth-password-recovery');
$routes->remove('auth-password-reset');
$routes->remove('auth-login');
$routes->remove('auth-logout');

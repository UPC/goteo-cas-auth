<?php

use Goteo\Application\App;
use Goteo\Application\Config;
use Symfony\Component\Routing\Route;

// Autoload additional Classes
Config::addAutoloadDir(__DIR__ . '/src');

// Override auth routes (no prefix)
$routes = App::getRoutes();

$routes->add('auth-cas-login', new Route('/cas-login', array(
    '_controller' => 'Goteo\Controller\AuthController\CAS::casLoginAction',
)));

$routes->add('auth-cas-logout', new Route('/cas-logout', array(
    '_controller' => 'Goteo\Controller\AuthController\CAS::casLogoutAction',
)));

$routes->remove('outh-login');
$routes->remove('auth-signup');
$routes->remove('auth-signup-old-route');
$routes->remove('auth-oauth-signup');
$routes->remove('auth-oauth-signup-old-route');
$routes->remove('auth-password-recovery');
$routes->remove('auth-password-reset');

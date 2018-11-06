<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;
use MyGiftBox\bd\Connection;
use MyGiftBox\controllers\HomeController;
use MyGiftBox\controllers\CatalogController;
use MyGiftBox\controllers\ConnectionController;
use MyGiftBox\controllers\BoxController;

Connection::setConfig('src/conf/conf.ini');
$db = Connection::makeConnection();

$ini = parse_ini_file('src/conf/conf.ini');

$db = new DB();

$db->addConnection([
	'driver' => $ini['driver'],
	'host' => $ini['host'],
	'database' => $ini['dbname'],
	'username' => $ini['username'],
	'password' => $ini['password'],
	'charset' => 'utf8',
	'collation' => 'utf8_unicode_ci',
	'prefix' => ''
]);

$db->setAsGlobal();
$db->bootEloquent();

session_start();

require('container.php');

$app = new \Slim\App($container);

$app->get('/','HomeController:displayHome')->setName('Home');

$app->get('/CreateAccount', 'ConnectionController:displayCreateAccount')->setName('CreateAccount');

$app->post('/CreateAccount', function($request, $response, $args){
	$controller = $this['ConnectionController'];
	$checkAccountCreation = $controller->checkAccountCreation($request, $response, $args);
	$router = $this->router;
	return $response->withRedirect($router->pathFor('Home', []));
})->setName("checkAccountCreation");

$app->get('/Connection', 'ConnectionController:displayConnection')->setName("Connection");

$app->get('/ConsultCatalog', 'CatalogController:displayCatalog')->setName('ConsultCatalog');

$app->get('/CreateBox', 'BoxController:displayCreationBox')->setName("CreateBox");

$app->post('/CreateBox', function($request, $response, $args){
	$controller = $this['BoxController'];
	$CreationBox = $controller->creationBox($request, $response, $args);
	$router = $this->router;
	return $response->withRedirect($router->pathFor('Home', []));
})->setName("CreationBox");

$app->post('/Connection', function($request, $response, $args){
	$controller = $this['ConnectionController'];
	$checkConnection = $controller->checkTheConnection($request, $response, $args);
	$router = $this->router;
	return $response->withRedirect($router->pathFor('Home', []));
})->setName("checkAccountCreation");

$app->get('/Exit', function($request, $response, $args){
	$controller = $this['ConnectionController'];
	$checkDestroySession = $controller->checkDestroySession($request, $response, $args);
	$router = $this->router;
	return $response->withRedirect($router->pathFor('Home', []));
})->setName('Disconnection');

$app->run();
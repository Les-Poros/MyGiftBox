<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;
use MyGiftBox\bd\Connection;
use MyGiftBox\controllers\Authentication;
use MyGiftBox\controllers\HomeController;
use MyGiftBox\controllers\CatalogController;
use MyGiftBox\controllers\ConnectionController;
use MyGiftBox\controllers\BoxController;
use MyGiftBox\controllers\PrestationController;
use MyGiftBox\controllers\PayController;

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

$app->get('/HomeConnect','HomeController:displayHomeConnect')->setName('HomeConnect');

$app->get('/CreateAccount', 'ConnectionController:displayCreateAccount')->setName('CreateAccount');

$app->post('/CreateAccount', function($request, $response, $args){
	$controller = $this['ConnectionController'];
	$checkAccountCreation = $controller->checkAccountCreation($request, $response, $args);
	$router = $this->router;
	return $response->withRedirect($router->pathFor('HomeConnect', []));
})->setName("checkAccountCreation");

$app->get('/Connection', 'ConnectionController:displayConnection')->setName("Connection");

$app->get('/ConsultCatalog', function($request, $response, $args){
	if (Authentication::checkConnection()) {
		$controller = $this['CatalogController'];
		$displayCatalog = $controller->displayCatalog($request, $response, $args);
	}
	else {
		$router = $this->router;
		return $response->withRedirect($router->pathFor('Home', []));
	}
})->setName('ConsultCatalog');

$app->get('/CreateBox', function($request, $response, $args){
	if (Authentication::checkConnection()) {
		$controller = $this['BoxController'];
		$displayCreationBox = $controller->displayCreationBox($request, $response, $args);
	}
	else {
		$router = $this->router;
		return $response->withRedirect($router->pathFor('Home', []));
	}
})->setName("CreateBox");

$app->post('/CreateBox', function($request, $response, $args){
	if (Authentication::checkConnection()) {
		$controller = $this['BoxController'];
		$CreationBox = $controller->creationBox($request, $response, $args);
		$router = $this->router;
		return $response->withRedirect($router->pathFor('ConsultCatalogPurchase', []));
	}
	else {
		$router = $this->router;
		return $response->withRedirect($router->pathFor('Home', []));
	}
})->setName("CreationBox");

$app->post('/Connection', function($request, $response, $args){
	$controller = $this['ConnectionController'];
	$checkConnection = $controller->checkTheConnection($request, $response, $args);
	$router = $this->router;
	return $response->withRedirect($router->pathFor('HomeConnect', []));
})->setName("checkAccountCreation");

$app->get('/Exit', function($request, $response, $args){
	if (Authentication::checkConnection()) {
		$controller = $this['ConnectionController'];
		$checkDestroySession = $controller->checkDestroySession($request, $response, $args);
		$router = $this->router;
		return $response->withRedirect($router->pathFor('Home', []));
	}
	else {
		$router = $this->router;
		return $response->withRedirect($router->pathFor('Home', []));
	}
})->setName('Disconnection');

$app->get('/Prestation/{id}', function($request, $response, $args){
	if (Authentication::checkConnection()) {
		$controller = $this['PrestationController'];
		$displayPrestation = $controller->displayPrestation($request, $response, $args);
	}
	else {
		$router = $this->router;
		return $response->withRedirect($router->pathFor('Home', []));
	}
})->setName("Prestation");


$app->get('/MyAccount', function($request, $response, $args){
	if (Authentication::checkConnection()) {
		$controller = $this['ConnectionController'];
		$displayAccount = $controller->displayAccount($request, $response, $args);}
	else {
		$router = $this->router;
		return $response->withRedirect($router->pathFor('Home', []));
	}
})->setName("MyAccount");

$app->post('/MyAccount', function($request, $response, $args){
	if (Authentication::checkConnection()) {
		$controller = $this['ConnectionController'];
		$modifMember = $controller->modifMember($request, $response, $args);
		$router = $this->router;
		return $response->withRedirect($router->pathFor('MyAccount', []));
    }
	else {
		$router = $this->router;
		return $response->withRedirect($router->pathFor('Home', []));
	}
})->setName("ModifAccount");

$app->get('/{box}/ConsultCatalogPurchase', function($request, $response, $args){
	if (Authentication::checkConnection()) {
		$controller = $this['CatalogController'];
		$displayCatalog = $controller->displayCatalogPurchase($request, $response, $args);
	}
	else {
		$router = $this->router;
		return $response->withRedirect($router->pathFor('Home', []));
	}
})->setName('ConsultCatalogPurchase');

$app->get('/{idCoffret}/Pay', function($request, $response, $args){
	if (Authentication::checkConnection()) {
		$controller = $this['PayController'];
		$displayPay = $controller->displayPay($request, $response, $args);
	}
	else {
		$router = $this->router;
		return $response->withRedirect($router->pathFor('Home', []));
	}
})->setName("Pay");

$app->post('/{idCoffret}/Pay', function($request, $response, $args){
	if (Authentication::checkConnection()) {
		$controller = $this['PayController'];
		$displayPay = $controller->checkPay($request, $response, $args);
		$router = $this->router;
		return $response->withRedirect($router->pathFor('HomeConnect', []));
	}
	else {
		$router = $this->router;
		return $response->withRedirect($router->pathFor('Home', []));
	}
})->setName("Pay");

$app->get('/EditBox/{id}', function($request, $response, $args){
	if (Authentication::checkConnection()) {
		$controller = $this['BoxController'];
		$displayEditMod = $controller->displayEditMod($request, $response, $args);
	}
})->setName('EditBox');

$app->post('/EditBox/{id}', function($request, $response, $args){
	if (Authentication::checkConnection()) {
		$controller = $this['BoxController'];
		$addMessage = $controller->checkAddMessage($request, $response, $args);
		$router = $this->router;
		return $response->withRedirect($router->pathFor('HomeConnect', []));
	}
})->setName('EditBox');


$app->run();
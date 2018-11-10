<?php

require_once __DIR__ . '/vendor/autoload.php';

use MyGiftBox\controllers\HomeController;
use MyGiftBox\controllers\CatalogController;
use MyGiftBox\controllers\ConnectionController;
use MyGiftBox\controllers\BoxController;
use MyGiftBox\controllers\PrestationController;
use MyGiftBox\controllers\PayController;
use MyGiftBox\controllers\AdminPrestationsController;

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

$container = new \Slim\Container($configuration);

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('src/views');
    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $container->get('request')->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container->get('router'), $basePath));
    return $view;
};

$container['HomeController'] = function ($c){
    $view = $c->get('view');
    return new HomeController($view);
};

$container['CatalogController'] = function ($c){
    $view = $c->get('view');
    return new CatalogController($view);
};

$container['ConnectionController'] = function ($c){
    $view = $c->get('view');
    return new ConnectionController($view);
};

$container['BoxController'] = function ($c){
    $view = $c->get('view');
    return new BoxController($view);
};

$container['PrestationController'] = function ($c){
    $view = $c->get('view');
    return new PrestationController($view);
};

$container['PayController'] = function ($c){
    $view = $c->get('view');
    return new PayController($view);
};

$container['AdminPrestationsController'] = function($c) {
    $view = $c->get('view');
    return new AdminPrestationsController($view);
};

$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $response->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->write("Cette page n'existe pas");
    };
};

?>
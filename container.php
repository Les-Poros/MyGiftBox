<?php

require_once __DIR__ . '/vendor/autoload.php';

use MyGiftBox\controllers\HomeController;
use MyGiftBox\controllers\ConnectionController;
use MyGiftBox\controllers\BoxController;

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

$container['ConnectionController'] = function ($c){
    $view = $c->get('view');
    return new ConnectionController($view);
};

$container['BoxController'] = function ($c){
    $view = $c->get('view');
    return new BoxController($view);
};
?>
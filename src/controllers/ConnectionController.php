<?php

namespace MyGiftBox\controllers;

use \Slim\Views\Twig as twig;
use MyGiftBox\views\CreateAccountView;

class ConnectionController{
    
    public function __construct(twig $view) {
        $this->view = $view;
    }

    public function displayCreateAccount($request, $response, $args) {
		
		return $this->view->render($response, 'CreateAccountView.html.twig', []);
	}
}
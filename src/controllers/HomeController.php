<?php

namespace MyGiftBox\controllers;

use \Slim\Views\Twig as twig;
use MyGiftBox\controllers\Authentication;
use MyGiftBox\views\HomeView;

/**
 * Class HomeController
 */
class HomeController {

	protected $view;

	/**
	 * Constructor of the class HomeController
	 * @param view
	 */
    public function __construct(twig $view) {
        $this->view = $view;
    }

	/**
	 * Method that displays the home
	 * @param request
	 * @param response
	 * @param args
	 */
	public function displayHome($request, $response, $args) {
		if (Authentication::checkConnection()) {
			$nomMembre = $_SESSION['nomMembre'].' '.$_SESSION['prenomMembre'];
		}
		else {
			$nomMembre = "";
		}
		return $this->view->render($response, 'HomeView.html.twig', ['nomMembre' => $nomMembre,]);
	}

}
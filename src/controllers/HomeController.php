<?php

namespace MyGiftBox\controllers;

use MyGiftBox\models\Prestation;
use MyGiftBox\models\Membre;
use MyGiftBox\controllers\Authentication;
use MyGiftBox\controllers\BoxController;
use \Slim\Views\Twig as twig;
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
	 * Method that displays the home connect if we are in the database
	 * @param request
	 * @param response
	 * @param args
	 */
	public function displayHomeConnect($request, $response, $args) {
		if (Authentication::checkConnection()) {		
			$resDisplayBoxMember = BoxController::displayBoxMember($request, $response, $args);
			$nameMember = $_SESSION['forenameMember'];
			return $this->view->render($response, 'HomeConnectView.html.twig', [
				'nameMember' => $nameMember,
				'roleMember' => $_SESSION['roleMember'],
				'resDisplayBoxMember' => $resDisplayBoxMember,
			]);
		}
	}

	/**
	 * Method that displays the home when we are not in the database
	 * @param request
	 * @param response
	 * @param args
	 */
	public function displayHome($request, $response, $args) {
		$prestations = array();
		$randomPrestations = Prestation::inRandomOrder()->select('img')->take(9)->get()->toArray();	
		if (Authentication::checkConnection()) {
            $nameMember = $_SESSION['forenameMember'];
            $roleMember = $_SESSION['roleMember'];
        } else {
            $nameMember = "";
            $roleMember = 0;
        }
		return $this->view->render($response, 'HomeView.html.twig', [
			'randomPrestations' => $randomPrestations,
			'nameMember' => $nameMember,
			'roleMember' => $roleMember,
		]);
	}

}
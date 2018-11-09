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
	 * Method that displays the home
	 * @param request
	 * @param response
	 * @param args
	 */
	public function displayHomeConnect($request, $response, $args) {
		if (Authentication::checkConnection()) {		
			$variables = BoxController::displayBoxMember($request, $response, $args);
			$nomMembre = $_SESSION['forenameMember'];
			return $this->view->render($response, 'HomeConnectView.html.twig', [
				  'nomMembre' => $nomMembre,
				  'role' => $_SESSION['roleMember'],
				  'variables' => $variables,
			]);
		}
	}

	/**
	 * 
	 * @param request
	 * @param response
	 * @param args
	 */
	public function displayHome($request, $response, $args) {
		if (Authentication::checkConnection()) {
			$nomMembre = $_SESSION['forenameMember'];
		} else {
			$nomMembre = "";
		}
		$prestations = array();
		$prestations = Prestation::inRandomOrder()->select('img')->take(9)->get()->toArray();	
		if (Authentication::checkConnection()) {
            $nomMembre = $_SESSION['forenameMember'];
            $role=$_SESSION['roleMember'];
        } else {
            $nomMembre = "";
            $role=0;
        }
		return $this->view->render($response, 'HomeView.html.twig', [
			'prestations' => $prestations,
			'nomMembre' => $nomMembre,
			'role' => $role,
		]);
	}

}
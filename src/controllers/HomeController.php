<?php

namespace MyGiftBox\controllers;

use \Slim\Views\Twig as twig;
use MyGiftBox\controllers\Authentication;
use MyGiftBox\controllers\BoxController;
use MyGiftBox\views\HomeView;
use MyGiftBox\models\Prestation;




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
			
			BoxController::displayBox($request, $response, $args);
			$nomMembre = $_SESSION['prenomMembre'];
			return $this->view->render($response, 'HomeConnectView.html.twig', [
		  		'nomMembre' => $nomMembre,
			]);
	
		}
		else {
			$nomMembre = "";		
			$prestations = array();
			$prestations = Prestation::inRandomOrder()->select('img')->take(9)->get()->toArray();	
			return $this->view->render($response, 'HomeView.html.twig', [
				'prestations' => $prestations,
		  'nomMembre' => $nomMembre,
			]);
		}	

		
	}

	public function displayHome($request, $response, $args) {
		if (Authentication::checkConnection()) {
			$nomMembre = $_SESSION['prenomMembre'];
		}else
		$nomMembre = "";
		$prestations = array();
		$prestations = Prestation::inRandomOrder()->select('img')->take(9)->get()->toArray();	
			return $this->view->render($response, 'HomeView.html.twig', [
				'prestations' => $prestations,
		        'nomMembre' => $nomMembre,
			]);
	}

	
}
<?php

namespace MyGiftBox\controllers;

use \Slim\Views\Twig as twig;
use MyGiftBox\controllers\Authentication;
use MyGiftBox\controllers\BoxController;
use MyGiftBox\views\AdminPrestationsView;
use MyGiftBox\models\Prestation;
use MyGiftBox\models\Membre;

/**
 * Class AdminPrestationsController
 */
class AdminPrestationsController {

	protected $view;

	/**
	 * Constructor of the class AdminPrestationsController
	 * @param view
	 */
    public function __construct(twig $view) {
        $this->view = $view;
    }

	/**
	 * Method that displays the page for administrate prestations
	 * @param request
	 * @param response
	 * @param args
	 */
	public function displayAdminPrestations($request, $response, $args) {
        return $this->view->render($response, 'AdminPrestationsView.html.twig', [
            'nomMembre' => $_SESSION['prenomMembre'],
			'role' => $_SESSION['roleMembre'],
        ]);
	}
	
	public function displayAddPrestation($request, $response, $args) {
        return $this->view->render($response, 'AddPrestationView.html.twig', [
            'nomMembre' => $_SESSION['prenomMembre'],
			'role' => $_SESSION['roleMembre'],
        ]);
	}
	
	public function checkAddPrestation($request, $response, $args) {
        /*return $this->view->render($response, 'AdminPrestationsView.html.twig', [
            'nomMembre' => $_SESSION['prenomMembre'],
			'role' => $_SESSION['roleMembre'],
        ]);*/
    }
    
}
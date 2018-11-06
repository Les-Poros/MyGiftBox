<?php

namespace MyGiftBox\controllers;

use \Slim\Views\Twig as twig;
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
	public function displayHome($request, $response, $args) {
		$prestations = array();
		$listPrest = Prestation::select('img')->get()->toArray();
		shuffle($listPrest);
		for($i=0;$i<9;$i++){
			$prestations[$i]=$listPrest[$i];
		}
		
		return $this->view->render($response, 'HomeView.html.twig', [
			'prestations' => $prestations,
		]);
	}

}
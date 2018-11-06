<?php

namespace MyGiftBox\controllers;

use \Slim\Views\Twig as twig;
use MyGiftBox\views\CreationBoxView;
use MyGiftBox\models\Coffret;

/**
 * Class BoxController
 */
class BoxController {

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
	public function displayCreationBox($request, $response, $args) {

			
		return $this->view->render($response, 'CreationBoxView.html.twig', [
		
		]);

    }

    public function CreationBox(){
        $nameBox = filter_var($_POST['nameBox'],FILTER_SANITIZE_STRING);
        $messageBox = filter_var($_POST['messageBox'],FILTER_SANITIZE_STRING);
        $dateBox = filter_var($_POST['dateBox'],FILTER_SANITIZE_DATE);
        
        $membre= m\Membre::where('mailMembre','=',$_SESSION['mailMembre'])->first();

        $box = new m\Coffret();
        $box->nomCoffret = $nameBox;
        $box->messageCoffret = $messageBox;
        $box->dateOuvertureCoffret = $dateBox;
        $idMember->idMembre = $membre['idMembre'];

        $box->save();
    }
    
}
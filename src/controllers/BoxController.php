<?php

namespace MyGiftBox\controllers;

use \Slim\Views\Twig as twig;
use MyGiftBox\views\CreationBoxView;
use MyGiftBox\models\Prestation;
use MyGiftBox\models\Membre;
use MyGiftBox\models\Coffret;
use MyGiftBox\models\ContenuCoffret;

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
        $dateBox = $_POST['dateBox'];
        
        $membre= Membre::where('mailMembre','=',$_SESSION['mailMembre'])->first();
        $box = new Coffret();
        $box->nomCoffret = $nameBox;
        $box->messageCoffret = $messageBox;
        $box->dateOuvertureCoffret = $dateBox;
        $box->idMembre = $membre['idMembre'];
        $box->estOuvert = 0;
        $box->msgRemerciement = "";

        $box->save();
    }

    public function displayBox($request, $response, $args){
        $mail = $_SESSION['mailMembre'];
			//récupère id du membre connecté
			$member= Membre::where('mailMembre', '=', $mail);
			$memberFirst = $member->first();
			$idMember = $memberFirst->idMembre;
			
			//récupère id du coffret
			$coffret = Coffret::where('idMembre','=',$idMember);
			$coffretFirst = $coffret->first();
			$idCoffret = $coffretFirst->idCoffret;

			//récupère le nom du coffret
			$nomCoffret = $coffretFirst->nomCoffret;
			
			//récupère id Prestation
			$prestation = ContenuCoffret::where('idCoffret','=',$idCoffret);
			$prestationFirst = $prestation->first();
			$idPrestation = $prestationFirst->idPrestation;

			//récupère img de la prestation
			$image = Prestation::where('idPrestation','=',$idPrestation);
			$imageFirst = $image->first();
			$lienImage = $imageFirst->img;

			
    }
    
}
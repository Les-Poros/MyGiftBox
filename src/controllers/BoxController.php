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
		$nomMembre = $_SESSION['prenomMembre'];
		return $this->view->render($response, 'CreationBoxView.html.twig', [
            'nomMembre' => $nomMembre,
		]);

    }

    public function creationBox(){
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
        $box->estPaye = 0;
        $box->hasContenuCoffret = 0;
        $box->msgRemerciement = "";

        $box->save();
    }

    public static function displayBox($request, $response, $args){
            $mail = $_SESSION['mailMembre'];
			//récupère id du membre connecté
			$member= Membre::where('mailMembre', '=', $mail);
			$memberFirst = $member->first();
			$idMember = $memberFirst->idMembre;
            //On vérifie si le membre connecté à déjà un coffret
            $memberHaveBox = false;
            $listMembre = Coffret::select('idMembre')->get()->toArray();
            foreach($listMembre as $values){
                $idMembre = $values['idMembre'];
                if($idMember == $idMembre ){
                    $memberHaveBox = true;
                }
            }


            // On vérifie si il y a du contenu dans le coffret
            if($memberHaveBox){
                $isContenuList = Coffret::select('hasContenuCoffret','nomCoffret','idCoffret')->where('idMembre','=',$idMember)->get()->toArray();
                $nomCoffretListe = array();
                foreach($isContenuList as $values){
                    $isContenu = $values['hasContenuCoffret'];
                    $nomCoffret = $values['nomCoffret'];
                    $idCoffret = $values['idCoffret'];
                    if($isContenu == 0 ){
                        $imgDefault = "defaultBox.png";
                        array_push($nomCoffretListe,[$nomCoffret,$imgDefault]);
                    }
                   else{
                        $idPrestation = ContenuCoffret::select('idPrestation')->where('idCoffret','=',$idCoffret)->first()->toArray();
                        $prestation = Prestation::select('img')->where('idPrestation','=',$idPrestation)->first()->toArray();
                        $imgPrestation = $prestation['img'];
                        array_push($nomCoffretListe,[$nomCoffret,$imgPrestation]);
                   }
                }
               
                return($nomCoffretListe);
        }

            //Si il a un ou des coffrets, on les affiches
            // if($memberHaveBox){
			// //récupère id du coffret
            // $coffret = Coffret::where('idMembre','=',$idMember)->get()->toArray();
            // $infoCoffret = array();
            // foreach($coffret as $values) {
            //     $nomCoffret = $values['nomCoffret'];
            //     $idCoffret = $values['idCoffret'];
            //     $idPrestation = ContenuCoffret::select('idPrestation')->where('idCoffret','=',$idCoffret)->first()->toArray();
            //     $prestation = Prestation::select('img')->where('idPrestation','=',$idPrestation)->first()->toArray();
            //     $imgPrestation = $prestation['img'];
                
    
            //    array_push($infoCoffret,[$nomCoffret,$imgPrestation]);
            // }
            // return $infoCoffret;

        // }
			
    }
    
}
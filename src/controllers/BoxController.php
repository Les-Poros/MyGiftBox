<?php

namespace MyGiftBox\controllers;

use \Slim\Views\Twig as twig;
use MyGiftBox\views\CreationBoxView;
use MyGiftBox\views\ShareBoxView;
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
			'role' => $_SESSION['roleMembre'],
		]);

    }
    public function displayEditBox($request, $response, $args) {
		$nomMembre = $_SESSION['prenomMembre'];
		return $this->view->render($response, 'EditBoxView.html.twig', [
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
        $box->estCree = 1;
        $box->estValide = 0;
        $box->estOuvert = 0;
        $box->estPaye = 0;
        $box->estTransmis = 0;
        $box->hasContenuCoffret = 0;
        $box->msgRemerciement = "";
        $box->tokenCoffret = "";

        $box->save();
        return $box->idCoffret;
    }

    public static function displayBox($request, $response, $args){
            $mail = $_SESSION['mailMembre'];
			//récupère id du membre connecté
			$coffret= Membre::where('mailMembre', '=', $mail);
			$coffretFirst = $coffret->first();
			$idcoffret = $coffretFirst->idMembre;
            //On vérifie si le membre connecté à déjà un coffret
            $coffretHaveBox = false;
            $listMembre = Coffret::select('idMembre')->get()->toArray();
            foreach($listMembre as $values){
                $idMembre = $values['idMembre'];
                if($idcoffret == $idMembre ){
                    $coffretHaveBox = true;
                }
            }


            // On vérifie si il y a du contenu dans le coffret
            if($coffretHaveBox){
                $isContenuList = Coffret::select('hasContenuCoffret','nomCoffret','idCoffret','estPaye')->where('idMembre','=',$idcoffret)->get()->toArray();
                $nomCoffretListe = array();
                foreach($isContenuList as $values){
                    $isContenu = $values['hasContenuCoffret'];
                    $nomCoffret = $values['nomCoffret'];
                    $idCoffret = $values['idCoffret'];
                    $estPaye = $values['estPaye'];
                    if($isContenu == 0 ){
                        $imgDefault = "defaultBox.png";
                        array_push($nomCoffretListe,[$nomCoffret,$imgDefault,$idCoffret,$estPaye]);
                    }
                   else{
                        $idPrestation = ContenuCoffret::select('idPrestation')->where('idCoffret','=',$idCoffret)->first()->toArray();
                        $prestation = Prestation::select('img')->where('idPrestation','=',$idPrestation)->first()->toArray();
                        $imgPrestation = $prestation['img'];
                        array_push($nomCoffretListe,[$nomCoffret,$imgPrestation,$idCoffret,$estPaye]);
                   }
                }
               
                return($nomCoffretListe);
        }
			
    }

    public function displayEditMod($request, $response, $args){
        $idBox = $args['id'];
        $nomCoffret = Coffret::select('nomCoffret')->where('idCoffret','=',$idBox)->first()->toArray();
        $dateCoffret = Coffret::select('dateOuvertureCoffret')->where('idCoffret','=',$idBox)->first()->toArray(); 
        $infoList = array();
        $prixList = array();
        $totalPrice = 0;
        $contenuCoffret = ContenuCoffret::select('idPrestation')->where('idCoffret','=',$idBox)->get()->toArray();
        foreach($contenuCoffret as $values){
            $quantite = ContenuCoffret::select('quantite')->where('idPrestation','=',$values)->get()->toArray();
            $prixCoffret= Prestation::select('prix')->where('idPrestation','=',$values)->get()->toArray();
            $img = Prestation::select('img')->where('idPrestation','=',$values)->get()->toArray();
           array_push($infoList,[$img[0]['img'],$quantite[0]['quantite']]);
           array_push($prixList,$prixCoffret[0]['prix']);
        }

        foreach($prixList as $values){
            $totalPrice += $values;
        }
        
        return $this->view->render($response, 'BoxView.html.twig', [
            'info' => $infoList,
            'nomCoffret' => $nomCoffret['nomCoffret'],
            'idBox' => $idBox,
            'date' => $dateCoffret['dateOuvertureCoffret'],
            'prix' => $totalPrice,
        ]);
    }
   

    public function checkEditBox($request, $response, $args){
        $idBox = $args['id'];
        $nameBox = filter_var($_POST['nameBox'],FILTER_SANITIZE_SPECIAL_CHARS);
        $messageBox = filter_var($_POST['messageBox'],FILTER_SANITIZE_STRING);
        $dateBox = $_POST['dateBox'];
        
        self::editBox($nameBox,$messageBox,$dateBox,$idBox);
    }

    public static function editBox($nameBox,$messageBox,$dateBox,$idBox){
        $coffret = Coffret::where('idCoffret','=',$idBox)->first();
        //si les champs sont vides ont laisse ceux qui sont dans la base
        if($nameBox){
            $coffret->nomCoffret = $nameBox;
        }
        if($messageBox){
            $coffret->messageCoffret = $messageBox;
        }

        if($dateBox){
            $coffret->dateOuvertureCoffret = $dateBox;
        }
        
        $coffret->save();
    }

    private static function generateToken() {
        return bin2hex(openssl_random_pseudo_bytes(16));
    }

    public function shareBox($request, $response, $args){
        $nomMembre = $_SESSION['prenomMembre'];

        $box = Coffret::select('hasContenuCoffret','nomCoffret','idCoffret','tokenCoffret')->where('idCoffret','=',$args['idCoffret'])->first();

        if( $box['tokenCoffret']=="" ){
            $token = self::generateToken();
    
            $box->tokenCoffret = $token;
            $box->save();
        }
        else{
            $token = $box['tokenCoffret'];
        }

        $url = "http://" . $_SERVER["SERVER_NAME"];

		return $this->view->render($response, 'ShareBoxView.html.twig', [
            'nomMembre' => $nomMembre,
            'box' => $box['nomCoffret'],
            'token' => $token,
            'url' => $url,
		]);
    }

    public function displayLink($request, $response, $args){
        $nomMembre = $_SESSION['prenomMembre'];
        $token = $args['token'];
        
        $date = new \DateTime();

        $box = Coffret::where('tokenCoffret','=',$token)->first();
        $dateOuvertureCoffret = new \DateTime($box['dateOuvertureCoffret']);

        if ($date == $dateOuvertureCoffret) {
            $estOuvrable = true;
        } else if ($date > $dateOuvertureCoffret) {
            $estOuvrable = true;
        } else {
            $estOuvrable = false;
        }

        $presta = array();
        $contenuCoffret = ContenuCoffret::select('idPrestation')->where('idCoffret','=',$box['idCoffret'])->get()->toArray();
        foreach($contenuCoffret as $values){
            $prestation = Prestation::select('nomPrestation', 'descr', 'img')->where('idPrestation','=',$values)->first();
            $nomPrestation = $prestation['nomPrestation'];
            $descrPrestation = $prestation['descr'];
            $imgPrestation = $prestation['img'];
            $quantitePresta = ContenuCoffret::select('quantite')->where('idPrestation','=',$values)->first();
            $quantitePrestation = $quantitePresta['quantite'];
            array_push($presta,[$nomPrestation,$descrPrestation,$imgPrestation,$quantitePrestation]);
        }

        return $this->view->render($response, 'LinkBoxView.html.twig', [
            'nomMembre' => $nomMembre,
            'token' => $token,
            'nomCoffret' => $box['nomCoffret'],
            'messageCoffret' => $box['messageCoffret'],
            'date' => $box['dateOuvertureCoffret'],
            'listBox' => $presta,
            'estOuvrable' => $estOuvrable,
		]);
    }

    
}
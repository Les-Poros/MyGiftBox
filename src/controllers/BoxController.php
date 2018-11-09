<?php

namespace MyGiftBox\controllers;

use MyGiftBox\models\Prestation;
use MyGiftBox\models\Membre;
use MyGiftBox\models\Coffret;
use MyGiftBox\models\ContenuCoffret;
use \Slim\Views\Twig as twig;
use MyGiftBox\views\CreationBoxView;
use MyGiftBox\views\ShareBoxView;

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
		$memberName = $_SESSION['forenameMember'];
		return $this->view->render($response, 'CreationBoxView.html.twig', [
            'nomMembre' => $memberName,
			'role' => $_SESSION['roleMember'],
		]);

    }

    /**
	 * 
	 * @param request
	 * @param response
	 * @param args
	 */
    public function displayEditBox($request, $response, $args) {
        $box = Coffret::select('idMembre')->where('idCoffret','=',$args["id"])->first()->toArray();
        if ($_SESSION["idMember"] == $box["idMembre"]) {
            return $this->view->render($response, 'EditBoxView.html.twig', [
                'nomMembre' => $_SESSION['forenameMember'],
                'role' => $_SESSION['roleMember'],
            ]);
        } else {
            return $this->view->render($response, 'Fail.html.twig', [
                'nomMembre' => $_SESSION['forenameMember'],
                "message" => "Désolé, seul le membre possédant cette boite y à accès",
                'role' => $_SESSION['roleMember'],
            ]);
        }
    }

    /**
	 * 
	 * @param request
	 * @param response
	 * @param args
	 */
    public function creationBox(){
        $nameBox = filter_var($_POST['nameBox'],FILTER_SANITIZE_STRING);
        $messageBox = filter_var($_POST['messageBox'],FILTER_SANITIZE_STRING);
        $dateBox = $_POST['dateBox'];
        
        $membre= Membre::where('mailMembre','=',$_SESSION['mailMember'])->first();
        $box = new Coffret();
        $box->nomCoffret = $nameBox;
        $box->messageCoffret = $messageBox;
        $box->dateOuvertureCoffret = $dateBox;
        $box->idMembre = $membre['idMembre'];
        $box->estCree = 1;
        $box->estOuvert = 0;
        $box->estPaye = 0;
        $box->estTransmis = 0;
        $box->hasContenuCoffret = 0;
        $box->msgRemerciement = "";
        $box->tokenCoffret = "";
        $box->tokenCagnotte = "";
        $box->totalPaye = 0;

        $box->save();
        return $box->idCoffret;
    }

    /**
	 * 
	 * @param request
	 * @param response
	 * @param args
	 */
    public static function displayBoxMember($request, $response, $args){
        $mail = $_SESSION['mailMember'];
        // We get the id of the connected member
        $membre= Membre::where('mailMembre', '=', $mail)->first();
        $idMembre = $membre->idMembre;
        // We check if the connected member already has a box
        $boxHaveContenu = false;
        $memberBox = Coffret::select('hasContenuCoffret','nomCoffret','idCoffret','estPaye')->where('idMembre','=',$idMembre)->get()->toArray();
        // We check if there is content in the box
        $listBoxesName = array();
        foreach($memberBox as $values){
            $isContenu = $values['hasContenuCoffret'];
            $boxName = $values['nomCoffret'];
            $idBox = $values['idCoffret'];
            $isPay = $values['estPaye'];
            if ($isContenu == 0 ) {
                $imgDefault = "defaultBox.png";
                array_push($listBoxesName,[$boxName,$imgDefault,$idBox,$isPay]);
            } else {
                $idPrestation = ContenuCoffret::select('idPrestation')->where('idCoffret','=',$idBox)->first()->toArray();
                $prestation = Prestation::select('img')->where('idPrestation','=',$idPrestation)->first()->toArray();
                $imgPrestation = $prestation['img'];
                array_push($listBoxesName,[$boxName,$imgPrestation,$idBox,$isPay]);
            }
        }
        return($listBoxesName);	
    }

    /**
	 * 
	 * @param request
	 * @param response
	 * @param args
	 */
    public function displayBox($request, $response, $args){
        $memberName = $_SESSION['forenameMember'];
        $idBox = $args['id'];
        $box = Coffret::where('idCoffret','=',$idBox)->first()->toArray();

        $prestations = array();
        $priceList = array();
        $totalPrice = 0;
        $ContenuBox = ContenuCoffret::select('idPrestation','quantite')->where('idCoffret','=',$idBox)->get()->toArray();
        foreach($ContenuBox as $values){
            $presta= Prestation::where('idPrestation','=',$values["idPrestation"])->first();
            $prestaTab=$presta->toArray();
            $prestaTab['categorie'] = $presta->categorie()->first()->toArray()['nomCategorie'];
            $prestaTab['quantite']=$values["quantite"];
            array_push($prestations,$prestaTab);
            $totalPrice += $presta['prix']*$values['quantite'];
        }
        if ($_SESSION["idMember"]==$box["idMembre"]) {
            return $this->view->render($response, 'BoxView.html.twig', [
                'nomMembre' => $memberName,
                'listPrestations' => $prestations,
                'nomCoffret' => $box['nomCoffret'],
                'idBox' => $idBox,
                'date' => $box['dateOuvertureCoffret'],
                'prix' => $totalPrice,
                'paye' => $box['estPaye'],
                'message' => $box['messageCoffret'],
                'messageRemer'=> $box['msgRemerciement'],
                'ouvert' => $box['estOuvert'],
                'transmis' => $box['estTransmis'],
                'role' => $_SESSION['roleMember'],
            ]);
        } else {
            return $this->view->render($response, 'Fail.html.twig', [
                'nomMembre' => $_SESSION['forenameMember'],
                "message" => "Désolé, seul le membre possédant cette boite y à accès",
                'role' => $_SESSION['roleMember'],
            ]);
        }
    }
   
    /**
	 * 
	 * @param request
	 * @param response
	 * @param args
	 */
    public function checkEditBox($request, $response, $args){
        $idBox = $args['id'];
        $nameBox = filter_var($_POST['nameBox'],FILTER_SANITIZE_SPECIAL_CHARS);
        $messageBox = filter_var($_POST['messageBox'],FILTER_SANITIZE_STRING);
        $dateBox = $_POST['dateBox'];
        self::editBox($nameBox,$messageBox,$dateBox,$idBox);
    }

    /**
	 * 
	 * @param nameBox
	 * @param messageBox
	 * @param dateBox
     * @param idBox
	 */
    public static function editBox($nameBox,$messageBox,$dateBox,$idBox){
        $box = Coffret::where('idCoffret','=',$idBox)->first();
        // If the fields are empty, leave the ones in the database
        if ($nameBox) {
            $box->nomCoffret = $nameBox;
        }
        if ($messageBox) {
            $box->messageCoffret = $messageBox;
        }

        if ($dateBox) {
            $box->dateOuvertureCoffret = $dateBox;
        }
        $box->save();
    }

    /**
	 * 
	 */
    private static function generateToken() {
        return bin2hex(openssl_random_pseudo_bytes(16));
    }

    /**
	 * 
	 * @param request
	 * @param response
	 * @param args
	 */
    public function shareBox($request, $response, $args){
        $memberName = $_SESSION['forenameMember'];
        $idBox = $args['idCoffret'];
        $box = Coffret::where('idCoffret','=',$args['idCoffret'])->first();
        $box->estTransmis = 1;
        $box->save();
        if ($box['tokenCoffret'] == "") {
            $token = self::generateToken();
            $box->tokenCoffret = $token;
            $box->save();
        } else {
            $token = $box['tokenCoffret'];
        }

        $url = "http://" . $_SERVER["SERVER_NAME"];
        if($_SESSION["idMember"]==$box["idMembre"]) {
            return $this->view->render($response, 'ShareBoxView.html.twig', [
                'nomMembre' => $memberName,
                'box' => $box['nomCoffret'],
                'token' => $token,
                'url' => $url,
                'role' => $_SESSION['roleMember'],
            ]);
        } else {
            return $this->view->render($response, 'Fail.html.twig', [
                'nomMembre' => $_SESSION['forenameMember'],
                "message" => "Désolé, seul le membre possédant cette boite y à accès",
                'role' => $_SESSION['roleMember'],
            ]);
        }
    }

    /**
	 * 
	 * @param request
	 * @param response
	 * @param args
	 */
    public function displayLink($request, $response, $args){
        $token = $args['token'];
        $date = new \DateTime();
        $box = Coffret::where('tokenCoffret','=',$token)->first();
        $dateOuvertureCoffret = new \DateTime($box['dateOuvertureCoffret']);
        $box->estOuvert = 1;
        $box->save();
        if ($date == $dateOuvertureCoffret) {
            $estOuvrable = true;
        } else if ($date > $dateOuvertureCoffret) {
            $estOuvrable = true;
        } else {
            $estOuvrable = false;
        }

        $presta = array();
        $ContenuBox = ContenuCoffret::select('idPrestation')->where('idCoffret','=',$box['idCoffret'])->get()->toArray();
        foreach($ContenuBox as $values){
            $prestation = Prestation::select('nomPrestation', 'descr', 'img')->where('idPrestation','=',$values)->first();
            $nomPrestation = $prestation['nomPrestation'];
            $descrPrestation = $prestation['descr'];
            $imgPrestation = $prestation['img'];
            $quantitePresta = ContenuCoffret::select('quantite')->where('idPrestation','=',$values)->first();
            $quantitePrestation = $quantitePresta['quantite'];
            array_push($presta,[$nomPrestation,$descrPrestation,$imgPrestation,$quantitePrestation]);
        }

        return $this->view->render($response, 'LinkBoxView.html.twig', [
            'token' => $token,
            'nomCoffret' => $box['nomCoffret'],
            'messageCoffret' => $box['messageCoffret'],
            'date' => $box['dateOuvertureCoffret'],
            'msgRemerciement' => $box['msgRemerciement'],
            'listBox' => $presta,
            'estOuvrable' => $estOuvrable,
            'nom' => $_SESSION['nameMember'],
            'prenom' => $_SESSION['forenameMember'],
            'mail' => $_SESSION['mailMember'],
			'role' => $_SESSION['roleMember'],
		]);
    }

    /**
	 * 
	 * @param request
	 * @param response
	 * @param args
	 */
    public function sendThanks($request, $response, $args){
        $box = Coffret::where('tokenCoffret','=',$args['token'])->first();
        $box->msgRemerciement = $_POST['msgRemerciement'];
        $box->save();
    }

    
}
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
	 * Constructor of the class BoxController
	 * @param view
	 */
    public function __construct(twig $view) {
        $this->view = $view;
    }

	/**
	 * Method that displays the form for the creation of a box
	 * @param request
	 * @param response
	 * @param args
	 */
	public function displayCreationBox($request, $response, $args) {
		$memberName = $_SESSION['forenameMember'];
		return $this->view->render($response, 'CreationBoxView.html.twig', [
            'nameMember' => $memberName,
			'roleMember' => $_SESSION['roleMember'],
		]);

    }

    /**
	 * Method that displays the form for the modification of a box
	 * @param request
	 * @param response
	 * @param args
	 */
    public function displayEditBox($request, $response, $args) {
        $box = Coffret::select('idMembre')->where('idCoffret','=',$args["id"])->first()->toArray();
        if ($_SESSION["idMember"] == $box["idMembre"]) {
            return $this->view->render($response, 'EditBoxView.html.twig', [
                'nameMember' => $_SESSION['forenameMember'],
                'roleMember' => $_SESSION['roleMember'],
            ]);
        } else {
            return $this->view->render($response, 'Fail.html.twig', [
                'nameMember' => $_SESSION['forenameMember'],
                "message" => "Désolé, seul le membre possédant cette boite y a accès",
                'roleMember' => $_SESSION['roleMember'],
            ]);
        }
    }

    /**
	 * Method that creates a box
	 * @param request
	 * @param response
	 * @param args
	 */
    public function creationBox(){
        $nameBox = filter_var($_POST['nameBox'],FILTER_SANITIZE_STRING);
        $messageBox = filter_var($_POST['messageBox'],FILTER_SANITIZE_STRING);
        $dateBox = $_POST['dateBox'];
        $member = Membre::where('mailMembre','=',$_SESSION['mailMember'])->first();
        $box = new Coffret();
        $box->nomCoffret = $nameBox;
        $box->messageCoffret = $messageBox;
        $box->dateOuvertureCoffret = $dateBox;
        $box->idMembre = $member['idMembre'];
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
	 * Method that displays the boxes of a member
	 * @param request
	 * @param response
	 * @param args
	 */
    public static function displayBoxMember($request, $response, $args){
        $mail = $_SESSION['mailMember'];
        // We get the id of the connected member
        $member = Membre::where('mailMembre', '=', $mail)->first();
        $idMembre = $member->idMembre;
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
                $pictureDefault = "defaultBox.png";
                array_push($listBoxesName, [$boxName, $pictureDefault, $idBox, $isPay]);
            } else {
                $idPrestation = ContenuCoffret::select('idPrestation')->where('idCoffret','=',$idBox)->first()->toArray();
                $prestation = Prestation::select('img')->where('idPrestation','=',$idPrestation)->first()->toArray();
                $pictureDefault = $prestation['img'];
                array_push($listBoxesName, [$boxName, $pictureDefault, $idBox, $isPay]);
            }
        }
        return($listBoxesName);	
    }

    /**
	 * Method that displays the content of a box
	 * @param request
	 * @param response
	 * @param args
	 */
    public function displayBox($request, $response, $args){
        $memberName = $_SESSION['forenameMember'];
        $idBox = $args['id'];
        $box = Coffret::where('idCoffret','=',$idBox)->first()->toArray();

        $tabPrestations = array();
        $totalPrice = 0;
        $contentBox = ContenuCoffret::select('idPrestation','quantite')->where('idCoffret','=',$idBox)->get()->toArray();
        foreach($contentBox as $content){
            $prestation = Prestation::where('idPrestation','=',$content["idPrestation"])->first();
            $prestaTab = $prestation->toArray();
            $prestaTab['categorie'] = $prestation->categorie()->first()->toArray()['nomCategorie'];
            $prestaTab['quantite'] = $content["quantite"];
            array_push($tabPrestations, $prestaTab);
            $totalPrice += $prestaTab['prix']*$content['quantite'];
        }
        if ($_SESSION["idMember"] == $box["idMembre"]) {
            return $this->view->render($response, 'BoxView.html.twig', [
                'nameMember' => $memberName,
                'tabPrestations' => $tabPrestations,
                'nameBox' => $box['nomCoffret'],
                'idBox' => $idBox,
                'dateOpenBox' => $box['dateOuvertureCoffret'],
                'price' => $totalPrice,
                'isPay' => $box['estPaye'],
                'messageBox' => $box['messageCoffret'],
                'messageThanksBox'=> $box['msgRemerciement'],
                'isOpen' => $box['estOuvert'],
                'isTransmitted' => $box['estTransmis'],
                'roleMember' => $_SESSION['roleMember'],
                'tokenCagnotte' => $box['tokenCagnotte'],
            ]);
        } else {
            return $this->view->render($response, 'Fail.html.twig', [
                'nameMember' => $_SESSION['forenameMember'],
                "message" => "Désolé, seul le membre possédant cette boite y à accès",
                'roleMember' => $_SESSION['roleMember'],
            ]);
        }
    }
   
    /**
	 * Method that checks the edition of a box
	 * @param request
	 * @param response
	 * @param args
	 */
    public function checkEditBox($request, $response, $args){
        $idBox = $args['id'];
        $nameBox = filter_var($_POST['nameBox'],FILTER_SANITIZE_SPECIAL_CHARS);
        $messageBox = filter_var($_POST['messageBox'],FILTER_SANITIZE_STRING);
        $dateBox = $_POST['dateBox'];
        self::editBox($nameBox, $messageBox, $dateBox, $idBox);
    }

    /**
	 * Method that edits a box
	 * @param nameBox
	 * @param messageBox
	 * @param dateBox
     * @param idBox
	 */
    public static function editBox($nameBox, $messageBox, $dateBox, $idBox){
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
	 * Method that generates a token for a box
	 */
    private static function generateToken() {
        return bin2hex(openssl_random_pseudo_bytes(16));
    }

    /**
	 * Methat that shares a link for a box
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
            $tokenBox = self::generateToken();
            $box->tokenCoffret = $tokenBox;
            $box->save();
        } else {
            $tokenBox = $box['tokenCoffret'];
        }

        $urlBox = "http://" . $_SERVER["SERVER_NAME"];
        if($_SESSION["idMember"]==$box["idMembre"]) {
            return $this->view->render($response, 'ShareBoxView.html.twig', [
                'nameMember' => $memberName,
                'nameBox' => $box['nomCoffret'],
                'tokenBox' => $tokenBox,
                'urlBox' => $urlBox,
                'roleMember' => $_SESSION['roleMember'],
            ]);
        } else {
            return $this->view->render($response, 'Fail.html.twig', [
                'nameMember' => $_SESSION['forenameMember'],
                "message" => "Désolé, seul le membre possédant cette boite y à accès",
                'roleMember' => $_SESSION['roleMember'],
            ]);
        }
    }

    /**
	 * Method that displays the link for the sharing of a box
	 * @param request
	 * @param response
	 * @param args
	 */
    public function displayLink($request, $response, $args){
        $token = $args['token'];
        $date = new \DateTime();
        $box = Coffret::where('tokenCoffret','=',$token)->first();
        $idMember = $box['idMembre'];
        $member = Membre::where('idMembre','=',$idMember)->first();
          
        $dateOpenBox = new \DateTime($box['dateOuvertureCoffret']);
        $box->estOuvert = 1;
        $box->save();
        if ($date == $dateOpenBox) {
            $isBusiness = true;
        } else if ($date > $dateOpenBox) {
            $isBusiness = true;
        } else {
            $isBusiness = false;
        }

        $tabPrestations = array();
        $contentBox = ContenuCoffret::select('idPrestation')->where('idCoffret','=',$box['idCoffret'])->get()->toArray();
        foreach($contentBox as $content){
            $prestation = Prestation::select('nomPrestation', 'descr', 'img')->where('idPrestation','=',$content)->first();
            $namePrestation = $prestation['nomPrestation'];
            $descriptionPrestation = $prestation['descr'];
            $picturePrestation = $prestation['img'];
            $quantityPresta = ContenuCoffret::select('quantite')->where('idPrestation','=',$content)->first();
            $quantityPrestation = $quantityPresta['quantite'];
            array_push($tabPrestations, [$namePrestation,$descriptionPrestation,$picturePrestation,$quantityPrestation]);
        }

        if (Authentication::checkConnection()){
            $nameMember = $_SESSION['forenameMember'];
            $roleMember = $_SESSION['roleMember'];
        }
        else{
            $nameMember = "";
            $roleMember = 0;
        }

        return $this->view->render($response, 'LinkBoxView.html.twig', [
            'token' => $token,
            'nameBox' => $box['nomCoffret'],
            'messageBox' => $box['messageCoffret'],
            'dateOpenBox' => $box['dateOuvertureCoffret'],
            'messageThanksBox' => $box['msgRemerciement'],
            'tabPrestations' => $tabPrestations,
            'isBusiness' => $isBusiness,
            'name' => $member['prenomMembre'],
            'nameOfMember' => $member['nomMembre'],
            'mail' => $member['mailMembre'],
            'roleMember' => $roleMember,
            'nameMember' => $nameMember,
		]);
    }

    /**
	 * Method that sends the thanks to the member and modifies the messageRemerciement in the database
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
<?php

namespace MyGiftBox\controllers;

use MyGiftBox\models\Coffret;
use MyGiftBox\models\ContenuCoffret;
use MyGiftBox\models\Prestation;
use \Slim\Views\Twig as twig;

/**
 * Class PayController
 */
class PayController{

    protected $view;

	/**
	 * Constructor of the class PayController
	 * @param view
	 */
    public function __construct(twig $view) {
        $this->view = $view;
    }

    /**
	 * Method that display the form pay
	 * @param request
	 * @param response
	 * @param args
	 */
    public function displayPay($request, $response, $args) {
        $month = date("m");
        $year = date("Y");
        $box = Coffret::select('hasContenuCoffret','nomCoffret','idCoffret','idMembre')->where('idCoffret','=',$args['idCoffret'])->first()->toArray();
        $idPrestation = ContenuCoffret::select('idPrestation')->where('idCoffret','=',$box['idCoffret'])->get()->toArray();
        $tabPrestations = array();
        $sum = 0;
        foreach ($idPrestation as $p) {
            $prestation = Prestation::select('img','prix')->where('idPrestation','=',$p)->first()->toArray();
            $picturePrestation = $prestation['img'];
            $pricePrestation = $prestation['prix'];
            $quantityPresta = ContenuCoffret::select('quantite')->where('idPrestation','=',$p)->first()->toArray();
            $quantityPrestation = $quantityPresta['quantite'];
            $price = $pricePrestation * $quantityPrestation;
            $sum += $price;
            array_push($tabPrestations,[$picturePrestation, $quantityPrestation]);
        }
        
        if($_SESSION["idMember"] == $box["idMembre"]) {
            return $this->view->render($response, 'PayView.html.twig', [
                'month' => $month,
                'year' => $year,
                'nameMember' => $_SESSION['forenameMember'],
                'nameBox' => $box['nomCoffret'],
                'idBox' => $box['idCoffret'],
                'tabPrestations' => $tabPrestations,
                'sum' => $sum,
                'roleMember' => $_SESSION['roleMember'],
            ]);
        } else {
            return $this->view->render($response, 'Fail.html.twig', [
                'nomMembre' => $_SESSION['forenameMember'],
                'message'=>"Vous n'etes pas le responsable de cette box",
                'roleMember' => $_SESSION['roleMember'],
            ]);
        }
    }

    /**
	 * Method that displays the form for the choice for the payment
	 * @param request
	 * @param response
	 * @param args
	 */
    public function displayChoicePay($request, $response, $args) {
        $box = Coffret::select('idCoffret','tokenCagnotte')->where('idCoffret','=',$args['idCoffret'])->first()->toArray();
        $idPrestation = ContenuCoffret::select('idPrestation')->where('idCoffret','=',$box['idCoffret'])->get()->toArray();
        
        $urlBox = "http://" . $_SERVER["SERVER_NAME"];

        $tabCategories = array();
        $presta = array();
        foreach ($idPrestation as $p) {
            $prestation = Prestation::select('idCategorie')->where('idPrestation','=',$p)->first()->toArray();
            $quantitePresta = ContenuCoffret::select('quantite')->where('idPrestation','=',$p)->first()->toArray();
            $idCategory = $prestation['idCategorie'];
            if(in_array($idCategory, $tabCategories)==false){
                array_push($tabCategories, $idCategory);
            }
        }
        if(count($tabCategories)>=2)
		return $this->view->render($response, 'ChoicePayView.html.twig', [
            'nameMember' => $_SESSION['forenameMember'],
            'roleMember' => $_SESSION['roleMember'],
            'idBox' => $box['idCoffret'],
            'numberCategories' => count($tabCategories),
            'tokenBox' => $box['tokenCagnotte'],
            'urlBox' => $urlBox,
        ]);
        else
        return $this->view->render($response, 'Fail.html.twig', [
            'nameMember' => $_SESSION['forenameMember'],
            "message" => "Vous devez avoir des prestations d'au moins deux catégories différentes",
            'roleMember' => $_SESSION['roleMember'],
        ]);
    }

    /**
	 * Method that checks the payement of a box and modifies the box in the database
	 * @param request
	 * @param response
	 * @param args
	 */
    public function checkPay($request, $response, $args){
        $box = Coffret::where('idCoffret','=',$args['idCoffret'])->first();
        $box->estPaye = 1;
        $box->save();
    }

    // Method which generates a token for a pot
    private static function generateTokenPot() {
        return bin2hex(openssl_random_pseudo_bytes(16));
    }

    /**
	 * Method that displays the generation of a link for a pot
	 * @param request
	 * @param response
	 * @param args
	 */
    public function displayGeneratePot($request, $response, $args){
        $idBox = $args['idCoffret'];
        $box = Coffret::find($idBox);
        if ($box['tokenCagnotte'] == "") {
            $tokenBox = self::generateTokenPot();
            $box->tokenCagnotte = $tokenBox;
            $box->save();
        } else {
            $tokenBox = $box['tokenCagnotte'];
        }
        $urlBox = "http://" . $_SERVER["SERVER_NAME"];
        if($_SESSION["idMember"]==$box["idMembre"]){
            return $this->view->render($response, 'GeneratePotView.html.twig', [
                'nameMember' => $_SESSION['forenameMember'],
                'roleMember' => $_SESSION['roleMember'],
                'nameBox' => $box['nomCoffret'],
                'tokenBox' => $tokenBox,
                'urlBox' => $urlBox,
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
	 * Method that displays the page where they can participate to a pot
	 * @param request
	 * @param response
	 * @param args
	 */
    public function displayParticipatePot($request, $response, $args){
        $box = Coffret::select('hasContenuCoffret','nomCoffret','idCoffret','idMembre','totalPaye')->where('tokenCagnotte','=',$args['tokenPot'])->first()->toArray();
        $idPrestation = ContenuCoffret::select('idPrestation')->where('idCoffret','=',$box['idCoffret'])->get()->toArray();
        $tabCategories = array();
        $tabPrestations = array();
        $sum = 0;
        foreach ($idPrestation as $p) {
            $prestation = Prestation::select('img','prix','idCategorie')->where('idPrestation','=',$p)->first()->toArray();
            $picturePrestation = $prestation['img'];
            $pricePrestation = $prestation['prix'];
            $quantityPresta = ContenuCoffret::select('quantite')->where('idPrestation','=',$p)->first()->toArray();
            $quantityPrestation = $quantityPresta['quantite'];
            $price = $pricePrestation * $quantityPrestation;
            $sum += $price;
            $idCategory = $prestation['idCategorie'];
            if (in_array($idCategory, $tabCategories) == false) {
                array_push($tabCategories, $idCategory);
            }
            array_push($tabPrestations, [$picturePrestation, $quantityPrestation]);
        }
        $numberCategories = 0;
        foreach ($tabCategories as $category) {
            $numberCategories .= 1;
        }
        if (Authentication::checkConnection()) {
            $nameMember = $_SESSION['forenameMember'];
            $roleMember = $_SESSION['roleMember'];
        } else {
            $nameMember = "";
            $roleMember = 0;
        }
            return $this->view->render($response, 'ParticipatePotView.html.twig', [
                'nameMember' => $nameMember,
                'nameBox' => $box['nomCoffret'],
                'idBox' => $box['idCoffret'],
                'tabPrestations' => $tabPrestations,
                'sum' => $sum,
                'roleMember' => $roleMember,
                'numberCategories' => $numberCategories,
                'totalPayeBox' => $box['totalPaye'],
            ]);
        
    }

    /**
	 * Method that checks the participation to a pot and modifies the coffret in the database
	 * @param request
	 * @param response
	 * @param args
	 */
    public function checkParticipatePot($request, $response, $args){
        $tokenCagnotte = $args['tokenPot'];
        $box = Coffret::where('tokenCagnotte', '=', $tokenCagnotte)->first();
        $box->totalPaye += $_POST['participation'];
        $box->messageCoffret .= "\n".$_POST['msg'];

        $idPrestation = ContenuCoffret::select('idPrestation')->where('idCoffret','=',$box['idCoffret'])->get()->toArray();

        $sum = 0;
        foreach ($idPrestation as $p) {
            $prestation = Prestation::select('prix')->where('idPrestation','=',$p)->first()->toArray();
            $pricePrestation = $prestation['prix'];
            $quantityPresta = ContenuCoffret::select('quantite')->where('idPrestation','=',$p)->first()->toArray();
            $quantityPrestation = $quantityPresta['quantite'];
            $price = $pricePrestation * $quantityPrestation;
            $sum += $price;
        }
        if($box['totalPaye'] >= $sum){
            $box->estPaye = 1;
        }
        $box->save();
    }

}
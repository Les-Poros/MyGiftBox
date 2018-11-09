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
	 * 
	 * @param request
	 * @param response
	 * @param args
	 */
    public function displayPay($request, $response, $args) {
        $month = date("m");
        $year = date("Y");

        $box = Coffret::select('hasContenuCoffret','nomCoffret','idCoffret','idMembre')->where('idCoffret','=',$args['idCoffret'])->first()->toArray();
        $idPrestation = ContenuCoffret::select('idPrestation')->where('idCoffret','=',$box['idCoffret'])->get()->toArray();

        $presta = array();
        $somme = 0;
        foreach ($idPrestation as $p) {
            $prestation = Prestation::select('img','prix')->where('idPrestation','=',$p)->first()->toArray();
            $imgPrestation = $prestation['img'];
            $prixPrestation = $prestation['prix'];
            $quantitePresta = ContenuCoffret::select('quantite')->where('idPrestation','=',$p)->first()->toArray();
            $quantitePrestation = $quantitePresta['quantite'];
            $prix = $prixPrestation * $quantitePrestation;
            $somme += $prix;
            array_push($presta,[$imgPrestation,$quantitePrestation]);
        }
        
        if($_SESSION["idMembre"]==$box["idMembre"]) {
            return $this->view->render($response, 'PayView.html.twig', [
                'month' => $month,
                'year' => $year,
                'nomMembre' => $_SESSION['forenameMember'],
                'box' => $box['nomCoffret'],
                'idBox' => $box['idCoffret'],
                'presta' => $presta,
                'total' => $somme,
                'role' => $_SESSION['roleMember'],
            ]);
        } else {
            return $this->view->render($response, 'BoxMemberFail.html.twig', [
                'nomMembre' => $_SESSION['forenameMember'],
            ]);
        }
    }

    /**
	 * 
	 * @param request
	 * @param response
	 * @param args
	 */
    public function displayChoicePay($request, $response, $args) {
        $box = Coffret::select('idCoffret')->where('idCoffret','=',$args['idCoffret'])->first()->toArray();
        $idPrestation = ContenuCoffret::select('idPrestation')->where('idCoffret','=',$box['idCoffret'])->get()->toArray();
        
        $tabCateg = array();
        $presta = array();
        foreach ($idPrestation as $p) {
            $prestation = Prestation::select('idCategorie')->where('idPrestation','=',$p)->first()->toArray();
            $quantitePresta = ContenuCoffret::select('quantite')->where('idPrestation','=',$p)->first()->toArray();
            $idCateg= $prestation['idCategorie'];
            if( in_array($idCateg, $tabCateg)==false){
                array_push($tabCateg,$idCateg);
            }
        }
        $nbCateg=0;
        foreach ($tabCateg as $categ) {
            $nbCateg.=1;
        }
		return $this->view->render($response, 'ChoicePayView.html.twig', [
            'nomMembre' => $_SESSION['forenameMember'],
            'role' => $_SESSION['roleMember'],
            'idBox' => $box['idCoffret'],
            'nbCateg' => $nbCateg,
        ]);
    }

    /**
	 * 
	 * @param request
	 * @param response
	 * @param args
	 */
    public function checkPay($request, $response, $args){
        $box = Coffret::where('idCoffret','=',$args['idCoffret'])->first();
        $box->estPaye = 1;
        $box->save();
    }

    /**
	 * 
	 */
    private static function generateTokenPot() {
        return bin2hex(openssl_random_pseudo_bytes(16));
    }

    /**
	 * 
	 * @param request
	 * @param response
	 * @param args
	 */
    public function displayGeneratePot($request, $response, $args){
        $idBox = $args['idCoffret'];
        $box = Coffret::find($idBox);
        if ($box['tokenCagnotte'] == "") {
            $token = self::generateTokenPot();
            $box->tokenCagnotte = $token;
            $box->save();
        } else {
            $token = $box['tokenCagnotte'];
        }
        $url = "http://" . $_SERVER["SERVER_NAME"];
        if($_SESSION["idMembre"]==$box["idMembre"]){
            return $this->view->render($response, 'GeneratePotView.html.twig', [
                'nomMembre' => $_SESSION['forenameMember'],
                'box' => $box['nomCoffret'],
                'token' => $token,
                'url' => $url,
            ]);
        } else {
            return $this->view->render($response, 'BoxMemberFail.html.twig', [
                'nomMembre' => $_SESSION['forenameMember'],
            ]);
        }
    }

    /**
	 * 
	 * @param request
	 * @param response
	 * @param args
	 */
    public function displayParticipatePot($request, $response, $args){
        $box = Coffret::select('hasContenuCoffret','nomCoffret','idCoffret','idMembre','totalPaye')->where('tokenCagnotte','=',$args['tokenPot'])->first()->toArray();
        $idPrestation = ContenuCoffret::select('idPrestation')->where('idCoffret','=',$box['idCoffret'])->get()->toArray();
        $tabCateg = array();
        $presta = array();
        $somme = 0;
        foreach ($idPrestation as $p) {
            $prestation = Prestation::select('img','prix','idCategorie')->where('idPrestation','=',$p)->first()->toArray();
            $imgPrestation = $prestation['img'];
            $prixPrestation = $prestation['prix'];
            $quantitePresta = ContenuCoffret::select('quantite')->where('idPrestation','=',$p)->first()->toArray();
            $quantitePrestation = $quantitePresta['quantite'];
            $prix = $prixPrestation * $quantitePrestation;
            $somme += $prix;
            $idCateg= $prestation['idCategorie'];
            if (in_array($idCateg, $tabCateg) == false) {
                array_push($tabCateg, $idCateg);
            }
            array_push($presta, [$imgPrestation, $quantitePrestation]);
        }
        $nbCateg=0;
        foreach ($tabCateg as $categ) {
            $nbCateg .= 1;
        }
        if ($_SESSION["idMembre"] == $box["idMembre"]) {
            return $this->view->render($response, 'ParticipatePotView.html.twig', [
                'nomMembre' => $_SESSION['forenameMember'],
                'box' => $box['nomCoffret'],
                'idBox' => $box['idCoffret'],
                'presta' => $presta,
                'total' => $somme,
                'role' => $_SESSION['roleMember'],
                'nbCateg' => $nbCateg,
                'totalPaye' => $box['totalPaye'],
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
    public function checkParticipatePot($request, $response, $args){
        $tokenCagnotte = $args['tokenPot'];
        $box = Coffret::where('tokenCagnotte', '=', $tokenCagnotte)->first();
        $box->totalPaye += $_POST['participation'];
        $box->messageCoffret .= "\n".$_POST['msg'];
        $box->save();
    }

}
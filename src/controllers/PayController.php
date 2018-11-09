<?php

namespace MyGiftBox\controllers;
use MyGiftBox\models\Coffret;
use MyGiftBox\models\ContenuCoffret;
use MyGiftBox\models\Prestation;
use \Slim\Views\Twig as twig;

class PayController{
    
    public function __construct(twig $view) {
        $this->view = $view;
    }

    public function displayPay($request, $response, $args) {
        $month = date("m");
        $year = date("Y");

        $box = Coffret::select('hasContenuCoffret','nomCoffret','idCoffret','idMembre')->where('idCoffret','=',$args['idCoffret'])->first()->toArray();
        $idPrestation = ContenuCoffret::select('idPrestation')->where('idCoffret','=',$box['idCoffret'])->get()->toArray();
        
        $tabCateg = array();

        $presta = array();
        $somme = 0;
        foreach($idPrestation as $p){
            $prestation = Prestation::select('img','prix','idCategorie')->where('idPrestation','=',$p)->first()->toArray();
            $imgPrestation = $prestation['img'];
            $prixPrestation = $prestation['prix'];
            $quantitePresta = ContenuCoffret::select('quantite')->where('idPrestation','=',$p)->first()->toArray();
            $quantitePrestation = $quantitePresta['quantite'];
            $prix = $prixPrestation * $quantitePrestation;
            $somme += $prix;
            $idCateg= $prestation['idCategorie'];
            if( in_array($idCateg, $tabCateg)==false){
                array_push($tabCateg,$idCateg);
            }
            array_push($presta,[$imgPrestation,$quantitePrestation]);
        }
        $nbCateg=0;
        foreach($tabCateg as $categ){
            $nbCateg.=1;
        }
    if($_SESSION["idMembre"]==$box["idMembre"]){
		return $this->view->render($response, 'PayView.html.twig', [
            'month' => $month,
            'year' => $year,
            'nomMembre' => $_SESSION['prenomMembre'],
            'box' => $box['nomCoffret'],
            'idBox' => $box['idCoffret'],
            'presta' => $presta,
            'total' => $somme,
            'role' => $_SESSION['roleMembre'],
            'nbCateg' => $nbCateg,
        ]);
    }
    else
        return $this->view->render($response, 'BoxMemberFail.html.twig', [
            'nomMembre' => $_SESSION['prenomMembre'],
        ]);
    }

    public function displayChoicePay($request, $response, $args) {
        $box = Coffret::select('idCoffret')->where('idCoffret','=',$args['idCoffret'])->first()->toArray();
		return $this->view->render($response, 'ChoicePayView.html.twig', [
            'nomMembre' => $_SESSION['prenomMembre'],
            'role' => $_SESSION['roleMembre'],
            'idBox' => $box['idCoffret'],
        ]);
    }

    public function checkPay($request, $response, $args){
        $box = Coffret::where('idCoffret','=',$args['idCoffret'])->first();
        $box->estPaye = 1;
        $box->save();
    }

    private static function generateTokenPot() {
        return bin2hex(openssl_random_pseudo_bytes(16));
    }

    public function displayGeneratePot($request, $response, $args){
        $idBox = $args['idCoffret'];
        $box = Coffret::select('nomCoffret','tokenCagnotte','idMembre')->where('idCoffret','=',$args['idCoffret'])->first();
        if($box['tokenCagnotte']=="" ){
            $token = self::generateTokenPot();
            $box->tokenCagnotte = $token;
            $box->save();
        }
        else{
            $token = $box['tokenCagnotte'];
        }

        $url = "http://" . $_SERVER["SERVER_NAME"];
        if($_SESSION["idMembre"]==$box["idMembre"]){
		return $this->view->render($response, 'GeneratePotView.html.twig', [
            'nomMembre' => $_SESSION['prenomMembre'],
            'box' => $box['nomCoffret'],
            'token' => $token,
            'url' => $url,
        ]);
        }else
        return $this->view->render($response, 'BoxMemberFail.html.twig', [
            'nomMembre' => $_SESSION['prenomMembre'],
        ]);
    }

    public function displayParticipatePot($request, $response, $args){

        $box = Coffret::select('hasContenuCoffret','nomCoffret','idCoffret','idMembre')->where('tokenCagnotte','=',$args['tokenPot'])->first()->toArray();
        $idPrestation = ContenuCoffret::select('idPrestation')->where('idCoffret','=',$box['idCoffret'])->get()->toArray();
        
        $tabCateg = array();

        $presta = array();
        $somme = 0;
        foreach($idPrestation as $p){
            $prestation = Prestation::select('img','prix','idCategorie')->where('idPrestation','=',$p)->first()->toArray();
            $imgPrestation = $prestation['img'];
            $prixPrestation = $prestation['prix'];
            $quantitePresta = ContenuCoffret::select('quantite')->where('idPrestation','=',$p)->first()->toArray();
            $quantitePrestation = $quantitePresta['quantite'];
            $prix = $prixPrestation * $quantitePrestation;
            $somme += $prix;
            $idCateg= $prestation['idCategorie'];
            if( in_array($idCateg, $tabCateg)==false){
                array_push($tabCateg,$idCateg);
            }
            array_push($presta,[$imgPrestation,$quantitePrestation]);
        }
        $nbCateg=0;
        foreach($tabCateg as $categ){
            $nbCateg.=1;
        }
    if($_SESSION["idMembre"]==$box["idMembre"]){
		return $this->view->render($response, 'ParticipatePotView.html.twig', [
            'nomMembre' => $_SESSION['prenomMembre'],
            'box' => $box['nomCoffret'],
            'idBox' => $box['idCoffret'],
            'presta' => $presta,
            'total' => $somme,
            'role' => $_SESSION['roleMembre'],
            'nbCateg' => $nbCateg,
        ]);
    }
    else
        return $this->view->render($response, 'BoxMemberFail.html.twig', [
            'nomMembre' => $_SESSION['prenomMembre'],
        ]);
    }

}
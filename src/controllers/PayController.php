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

        $box = Coffret::select('hasContenuCoffret','nomCoffret','idCoffret')->where('idCoffret','=',$args['idCoffret'])->first()->toArray();
        $idPrestation = ContenuCoffret::select('idPrestation')->where('idCoffret','=',$box['idCoffret'])->get()->toArray();
        
        $presta = array();
        $somme = 0;
        foreach($idPrestation as $p){
            $prestation = Prestation::select('img','prix')->where('idPrestation','=',$p)->first()->toArray();
            $imgPrestation = $prestation['img'];
            $prixPrestation = $prestation['prix'];
            $quantitePresta = ContenuCoffret::select('quantite')->where('idPrestation','=',$p)->first()->toArray();
            $quantitePrestation = $quantitePresta['quantite'];
            $prix = $prixPrestation * $quantitePrestation;
            $somme += $prix;
            array_push($presta,[$imgPrestation,$quantitePrestation]);
        }
		return $this->view->render($response, 'PayView.html.twig', [
            'month' => $month,
            'year' => $year,
            'nomMembre' => $_SESSION['prenomMembre'],
            'box' => $box['nomCoffret'],
            'idBox' => $box['idCoffret'],
            'presta' => $presta,
            'total' => $somme,
        ]);
    }

    public function checkPay($request, $response, $args){
        $box = Coffret::where('idCoffret','=',$args['idCoffret'])->first();
        $box->estPaye = 1;
        $box->save();
    }
}
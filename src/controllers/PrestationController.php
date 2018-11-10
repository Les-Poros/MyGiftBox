<?php

namespace MyGiftBox\controllers;

use MyGiftBox\models\Categorie;
use MyGiftBox\models\Prestation;
use \Slim\Views\Twig as twig;
use MyGiftBox\views\PrestationView;
/**
 * Class PrestationController
 */
class PrestationController {

    protected $view;

	/**
	 * Constructor of the class PrestationController
	 * @param view
	 */
    public function __construct(twig $view) {
        $this->view = $view;
    }

    /**
	 * Method that displays the prestation
	 * @param request
	 * @param response
	 * @param args
	 */
    public function displayPrestation($request, $response, $args) {
        $idPrestation = $args['id'];
        $prestation = Prestation::where('idPrestation', '=', $idPrestation)->first()->toArray();
        $picturePrestation = $prestation['img'];
        $namePrestation = $prestation['nomPrestation'];
        $idCategory = $prestation['idCategorie'];
        $category = Categorie::where("idCategorie", "=", $idCategory)->first()->toArray();
        $categoryPrestation = $category['nomCategorie'];
        $descriptionPrestation = $prestation['descr'];
        $pricePrestation = $prestation['prix'];
        if (Authentication::checkConnection()){
            $nameMember = $_SESSION['forenameMember'];
            $roleMember = $_SESSION['roleMember'];
        }
        else{
            $nameMember = "";
            $roleMember = 0;
        }
        return $this->view->render($response, 'PrestationView.html.twig', [
            'nameMember' => $nameMember,
            'picturePrestation' => $picturePrestation,
            'namePrestation' => $namePrestation,
            'categoryPrestation' => $categoryPrestation,
            'descriptionPrestation' => $descriptionPrestation,
            'pricePrestation' => $pricePrestation,
            'roleMember' => $roleMember,
        ]);
    }

}
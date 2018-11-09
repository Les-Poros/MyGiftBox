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
        $prestation = Prestation::where('idPrestation','=',$idPrestation)->first()->toArray();
        $imgPrestation = $prestation['img'];
        $nomPrestation = $prestation['nomPrestation'];
        $idCategory = $prestation['idCategorie'];
        $category = Categorie::where("idCategorie","=",$idCategory)->first()->toArray();
        $categoriePrestation = $category['nomCategorie'];
        $descrPrestation = $prestation['descr'];
        $prixPrestation = $prestation['prix'];
		$nomMembre = $_SESSION['prenomMembre'];
        return $this->view->render($response, 'PrestationView.html.twig', [
            'nomMembre' => $nomMembre,
			'role' => $_SESSION['roleMembre'],
            'imgPrestation' => $imgPrestation,
            'nomPrestation' => $nomPrestation,
            'categoriePrestation' => $categoriePrestation,
            'descrPrestation' => $descrPrestation,
            'prixPrestation' => $prixPrestation,
        ]);
    }

}
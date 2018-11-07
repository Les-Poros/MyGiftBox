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
        $prestation = Prestation::find($idPrestation)->toArray();
        $imgPrestation = $prestation['img'];
        $nomPrestation = $prestation['nomPrestation'];
        $idCategory = $prestation['idCategorie'];
        $category = Categorie::find($idCategory)->toArray();
        $categoriePrestation = $category['nomCategorie'];
        $prixPrestation = $prestation['prix'];
        return $this->view->render($response, 'PrestationView.html.twig', [
            'imgPrestation' => $imgPrestation,
            'nomPrestation' => $nomPrestation,
            'categoriePrestation' =>$categoriePrestation,
            'prixPrestation' => $prixPrestation,
        ]);
    }

}
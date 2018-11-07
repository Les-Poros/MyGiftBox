<?php

namespace MyGiftBox\controllers;

use MyGiftBox\models\Categorie;
use MyGiftBox\models\Prestation;
use \Slim\Views\Twig as twig;
use MyGiftBox\views\CatalogView;
/**
 * Class CatalogController
 */
class CatalogController {

    protected $view;

	/**
	 * Constructor of the class CatalogController
	 * @param view
	 */
    public function __construct(twig $view) {
        $this->view = $view;
    }

    /**
	 * Method that displays the catalog
	 * @param request
	 * @param response
	 * @param args
	 */
    public function displayCatalog($request, $response, $args) {
        $listCategories = Categorie::select('nomCategorie')->get()->toArray();
        $prestations = Prestation::all();
        $prest = array();
        for($i=0; $i<sizeof($prestations); $i++) {
            $prest[$i]['img'] = $prestations[$i]['img'];
            $prest[$i]['nomPrestation'] = $prestations[$i]['nomPrestation'];
            $category = $prestations[$i]->categorie()->first()->toArray();
            $prest[$i]['categorie'] = $category['nomCategorie'];
            $prest[$i]['prix'] = $prestations[$i]['prix'];
        }
        return $this->view->render($response, 'CatalogView.html.twig', [
            'categAttention' => $listCategories[0]['nomCategorie'],
            'categActivite' => $listCategories[1]['nomCategorie'],
            'categRestauration' => $listCategories[2]['nomCategorie'],
            'categHebergement' => $listCategories[3]['nomCategorie'],
            'listPrestations' => $prest,
        ]);
    }

}
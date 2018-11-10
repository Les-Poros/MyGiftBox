<?php

namespace MyGiftBox\controllers;

use MyGiftBox\models\Coffret;
use MyGiftBox\models\ContenuCoffret;
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
        $listPrestations = Prestation::where('activation', '=', 1)->get();
        for ($i=0; $i<sizeof($prestations); $i++) {
            $prestations[$i]['categorie'] = $prestations[$i]->categorie()->first()->toArray()['nomCategorie'];
        }
        if (Authentication::checkConnection()) {
            $nameMember = $_SESSION['forenameMember'];
            $roleMember = $_SESSION['roleMember'];
        } else {
            $nameMember = "";
            $roleMember = 0;
        }
        return $this->view->render($response, 'CatalogView.html.twig', [
            'nameMember' => $nameMember,
            'listCategories' => $listCategories,
            'listPrestations' => $listPrestations,
			'roleMember' => $roleMember,
        ]);
    }

    /**
	 * Method that displays the catalog purchase
	 * @param request
	 * @param response
	 * @param args
	 */
    public function displayCatalogPurchase($request, $response, $args) {
        $listCategories = Categorie::select('nomCategorie')->get()->toArray();
        $prestations = Prestation::where('activation', '=', 1)->get();
        for ($i=0; $i<sizeof($prestations); $i++) {
            $prestations[$i]['categorie'] = $prestations[$i]->categorie()->first()->toArray()['nomCategorie'];
        }
        $box = Coffret::select("nomCoffret","idMembre","estPaye")->where('idCoffret', '=', $args["box"])->first()->toArray();
        $contentBox = ContenuCoffret::where("idCoffret","=",$args["box"])->get()->toArray();
        if ($_SESSION["idMember"]==$box["idMembre"]) {
            if ($box["estPaye"] == 1) {
                return $this->view->render($response, 'Fail.html.twig', [
                    'nameMember' => $_SESSION['forenameMember'],
                    "message" => "Désolé, mais il est impossible de modifier une box payée !",
                    'roleMember' => $_SESSION['roleMember'],
                ]);
            } else {
                return $this->view->render($response, 'CatalogPurchaseView.html.twig', [
                    "contentBox" => $contentBox,
                    'nameBox' => $box["nomCoffret"],
                    'nameMember' => $_SESSION['forenameMember'],
                    'listCategories' => $listCategories,
                    'listPrestations' => $listPrestations,
                    'roleMember' => $_SESSION['roleMember'],
                ]);
            }
        } else {
            return $this->view->render($response, 'Fail.html.twig', [
                'nameMember' => $_SESSION['forenameMember'],
                "message" => "Désolé, seul le membre possédant cette boite y a accès",
                'roleMember' => $_SESSION['roleMember'],
            ]);
        }
    }

    /**
	 * Method which allows the modification of the content of a box
	 * @param request
	 * @param response
	 * @param args
	 */
    public function modifCatalogPurchase($request, $response, $args){
        $content = ContenuCoffret::where("idCoffret","=",$args["box"])->delete();
        $box = Coffret::where("idCoffret","=",$args["box"])->first();
        if ($_POST["nbAct"] == 0) {
            $box->hasContenuCoffret=0;
            $box->save();
        } else {
            $box->hasContenuCoffret=1;
            $box->save();
            for ($i=0;$i<$_POST["nbAct"];$i++) {
                $addPresta = new ContenuCoffret;
                $addPresta->idCoffret = $args["box"];
                $addPresta->idPrestation = $_POST['presta'.$i];
                $addPresta->quantite = $_POST['nbpresta'.$i];
                $addPresta->save();
            }
        }
        return $box['idCoffret'];
    }

}
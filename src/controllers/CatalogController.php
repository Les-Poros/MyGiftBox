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
        $prestations = Prestation::where('activation', '=', 1)->get();
        $prest = array();
        for($i=0; $i<sizeof($prestations); $i++) {
            $prest[$i]['idPrestation'] = $prestations[$i]['idPrestation'];
            $prest[$i]['img'] = $prestations[$i]['img'];
            $prest[$i]['nomPrestation'] = $prestations[$i]['nomPrestation'];
            $category = $prestations[$i]->categorie()->first()->toArray();
            $prest[$i]['categorie'] = $category['nomCategorie'];
            $prest[$i]['prix'] = $prestations[$i]['prix'];
        }
		$nomMembre = $_SESSION['prenomMembre'];
        return $this->view->render($response, 'CatalogView.html.twig', [
            'nomMembre' => $nomMembre,
            'listCateg' => $listCategories,
            'listPrestations' => $prest,
			'role' => $_SESSION['roleMembre'],
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
        $prest = array();
        for($i=0; $i<sizeof($prestations); $i++) {
            $prest[$i]['idPrestation'] = $prestations[$i]['idPrestation'];
            $prest[$i]['img'] = $prestations[$i]['img'];
            $prest[$i]['nomPrestation'] = $prestations[$i]['nomPrestation'];
            $category = $prestations[$i]->categorie()->first()->toArray();
            $prest[$i]['categorie'] = $category['nomCategorie'];
            $prest[$i]['prix'] = $prestations[$i]['prix'];
        }
        $box = Coffret::select("nomCoffret","idMembre")->where('idCoffret', '=', $args["box"])->first()->toArray();
        $contenu=ContenuCoffret::where("idCoffret","=",$args["box"])->get()->toArray();
        $nomMembre = $_SESSION['prenomMembre'];
        if($_SESSION["idMembre"]==$box["idMembre"])
        return $this->view->render($response, 'CatalogPurchaseView.html.twig', [
            "contenu"=>$contenu,
            'box'=>$box["nomCoffret"],
            'nomMembre' => $nomMembre,
            'listCateg' => $listCategories,
            'listPrestations' => $prest,
			'role' => $_SESSION['roleMembre'],
        ]);
        else
        return $this->view->render($response, 'BoxMemberFail.html.twig', [
            'nomMembre' => $nomMembre,
        ]);
    }
    public function modifCatalogPurchase($request, $response, $args){
        $contenu=ContenuCoffret::where("idCoffret","=",$args["box"])->delete();
        $coffret=Coffret::where("idCoffret","=",$args["box"])->first();
        if($_POST["nbAct"]==0){
            $coffret->hasContenuCoffret=0;
            $coffret->save();
        }
        else{
            $coffret->hasContenuCoffret=1;
            $coffret->save();
        for($i=0;$i<$_POST["nbAct"];$i++){
            $addPresta = new ContenuCoffret;
            $addPresta->idCoffret = $args["box"];
            $addPresta->idPrestation = $_POST['presta'.$i];
            $addPresta->quantite = $_POST['nbpresta'.$i];
            $addPresta->save();
        }
     }
     return $coffret['idCoffret'];
    }
}
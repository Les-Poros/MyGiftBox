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
        for ($i=0; $i<sizeof($prestations); $i++) {
            $prestations[$i]['categorie'] = $prestations[$i]->categorie()->first()->toArray()['nomCategorie'];
        }
        if (Authentication::checkConnection()) {
            $nomMembre = $_SESSION['forenameMember'];
            $role=$_SESSION['roleMember'];
        } else {
            $nomMembre = "";
            $role=0;
        }
        return $this->view->render($response, 'CatalogView.html.twig', [
            'nomMembre' => $nomMembre,
            'listCateg' => $listCategories,
            'listPrestations' => $prestations,
			'role' => $role,
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
        $contenu=ContenuCoffret::where("idCoffret","=",$args["box"])->get()->toArray();
        if ($_SESSION["idMember"]==$box["idMembre"]) {
            if ($box["estPaye"] == 1) {
                return $this->view->render($response, 'Fail.html.twig', [
                    'nomMembre' => $_SESSION['forenameMember'],
                    "message" => "Désolé, mais il est impossible de modifié une box payé",
                    'role' => $_SESSION['roleMember'],
                ]);
            } else {
                return $this->view->render($response, 'CatalogPurchaseView.html.twig', [
                    "contenu"=>$contenu,
                    'box'=>$box["nomCoffret"],
                    'nomMembre' => $_SESSION['forenameMember'],
                    'listCateg' => $listCategories,
                    'listPrestations' => $prestations,
                    'role' => $_SESSION['roleMember'],
                ]);
            }
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
    public function modifCatalogPurchase($request, $response, $args){
        $contenu=ContenuCoffret::where("idCoffret","=",$args["box"])->delete();
        $coffret=Coffret::where("idCoffret","=",$args["box"])->first();
        if ($_POST["nbAct"] == 0) {
            $coffret->hasContenuCoffret=0;
            $coffret->save();
        } else {
            $coffret->hasContenuCoffret=1;
            $coffret->save();
            for ($i=0;$i<$_POST["nbAct"];$i++) {
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
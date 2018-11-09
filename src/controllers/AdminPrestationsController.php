<?php

namespace MyGiftBox\controllers;

use \Slim\Views\Twig as twig;
use MyGiftBox\controllers\Authentication;
use MyGiftBox\controllers\BoxController;
use MyGiftBox\views\AdminPrestationsView;
use MyGiftBox\models\Prestation;
use MyGiftBox\models\Membre;
use MyGiftBox\models\Categorie;
use MyGiftBox\models\ContenuCoffret;

/**
 * Class AdminPrestationsController
 */
class AdminPrestationsController {

	protected $view;

	/**
	 * Constructor of the class AdminPrestationsController
	 * @param view
	 */
    public function __construct(twig $view) {
        $this->view = $view;
    }

	/**
	 * Method that displays the page for administrate prestations
	 * @param request
	 * @param response
	 * @param args
	 */
	public function displayAdminPrestations($request, $response, $args) {
		if($_SESSION['roleMembre']==1)
        return $this->view->render($response, 'AdminPrestationsView.html.twig', [
			'nomMembre' => $_SESSION['prenomMembre'],
			'role' => $_SESSION['roleMembre'],
		]);
		else
            return $this->view->render($response, 'RightFailView.html.twig', [
                'nomMembre' => $_SESSION['prenomMembre'],
            ]);
	}
	
	public function displayAddPrestation($request, $response, $args) {
		$listCategories = Categorie::select('nomCategorie')->get()->toArray();
		if($_SESSION['roleMembre']==1)
        return $this->view->render($response, 'AddPrestationView.html.twig', [
            'nomMembre' => $_SESSION['prenomMembre'],
			'role' => $_SESSION['roleMembre'],
            'listCateg' => $listCategories,
		]);
		else
            return $this->view->render($response, 'RightFailView.html.twig', [
                'nomMembre' => $_SESSION['prenomMembre'],
            ]);
	}
	
	public function checkAddPrestation($request, $response, $args) {
		$prestation = new Prestation();
		$maxsize = 15728640;
		$erreur = "";
		$prestation->nomPrestation = filter_var($_POST['namePrestation'],FILTER_SANITIZE_STRING);
		$prestation->descr = $_POST['descrPrestation'];
		if ($_FILES['imgPrestation']['error'] > 0) {
			$erreur = "Erreur lors du transfert";
		}
		if ($_FILES['imgPrestation']['size'] > $maxsize) {
			$erreur = "Le fichier est trop gros";
		}
		$extensions_valides = array( 'jpg' , 'jpeg' , 'gif' , 'png' );
		$extension_upload = strtolower(substr(strrchr($_FILES['imgPrestation']['name'], '.')  ,1)  );
		if (!in_array($extension_upload,$extensions_valides)) {
			$erreur = "Extension incorrecte";
		}
		$extension = explode('/', $_FILES['imgPrestation']['type']);
		move_uploaded_file($_FILES['imgPrestation']['tmp_name'], 'web/img/'.$_FILES['imgPrestation']['name']);
		rename("web/img/".$_FILES['imgPrestation']['name'], "web/img/".$_POST['namePrestation'].".".$extension[1]);
		$prestation->img = $_POST['namePrestation'].".".$extension[1];
		$prestation->prix = $_POST['prixPrestation'];
		if ($_POST['activationPrestation'] == "Activée") {
			$prestation->activation = 1;
		}
		else {
			$prestation->activation = 0;
		}
		$categorie = Categorie::select('idCategorie', 'nomCategorie')->where('nomCategorie', '=', $_POST['categoriePrestation'])->first()->toArray();
		$prestation->idCategorie = $categorie['idCategorie'];
		$prestation->save();
		//return $erreur;
		$lastPrestation = Prestation::select('idPrestation')->where('nomPrestation', '=', $_POST['namePrestation'])->first()->toArray();
		return $lastPrestation['idPrestation'];
	}
	
	public function displayDeactivateReactivatePrestation($request, $response, $args) {
		$listCategories = Categorie::select('nomCategorie')->get()->toArray();
		$prestations = Prestation::all();
        $prest = array();
        for($i=0; $i<sizeof($prestations); $i++) {
            $prest[$i]['idPrestation'] = $prestations[$i]['idPrestation'];
            $prest[$i]['img'] = $prestations[$i]['img'];
            $prest[$i]['nomPrestation'] = $prestations[$i]['nomPrestation'];
            $category = $prestations[$i]->categorie()->first()->toArray();
            $prest[$i]['categorie'] = $category['nomCategorie'];
			$prest[$i]['prix'] = $prestations[$i]['prix'];
			$prest[$i]['activation'] = $prestations[$i]['activation'];
		}
		if($_SESSION['roleMembre']==1)
        return $this->view->render($response, 'DeactivateReactivatePrestationView.html.twig', [
            'nomMembre' => $_SESSION['prenomMembre'],
			'role' => $_SESSION['roleMembre'],
            'listPrestations' => $prest,
			'listCateg' => $listCategories,
		]);
		else
            return $this->view->render($response, 'RightFailView.html.twig', [
                'nomMembre' => $_SESSION['prenomMembre'],
            ]);
	}
	
	public function checkDeactivateReactivatePrestation($request, $response, $args) {
		$idPrestation = $_POST['idPrestation'];
		$prestation = Prestation::where("idprestation",'=',$idPrestation)->first();
		if (isset($_POST['activatePrestation'])) {
			if ($_POST['activatePrestation'] == 'v') {
				$prestation->activation = 1;
			}
		}
		if (isset($_POST['deactivatePrestation'])) {
			if ($_POST['deactivatePrestation'] == 'x') {
				$prestation->activation = 0;
			}
		}
		$prestation->save();
	}
	
	public function displayDeletePrestation($request, $response, $args) {
		$listCategories = Categorie::select('nomCategorie')->get()->toArray();
		$prestations = Prestation::all();
        $prest = array();
        for($i=0; $i<sizeof($prestations); $i++) {
            $prest[$i]['idPrestation'] = $prestations[$i]['idPrestation'];
            $prest[$i]['img'] = $prestations[$i]['img'];
            $prest[$i]['nomPrestation'] = $prestations[$i]['nomPrestation'];
            $category = $prestations[$i]->categorie()->first()->toArray();
            $prest[$i]['categorie'] = $category['nomCategorie'];
			$prest[$i]['prix'] = $prestations[$i]['prix'];
		}
		if($_SESSION['roleMembre']==1)
        return $this->view->render($response, 'DeletePrestationView.html.twig', [
            'nomMembre' => $_SESSION['prenomMembre'],
			'role' => $_SESSION['roleMembre'],
            'listPrestations' => $prest,
			'listCateg' => $listCategories,
		]);
		else
            return $this->view->render($response, 'RightFailView.html.twig', [
                'nomMembre' => $_SESSION['prenomMembre'],
            ]);
	}
	
	public function checkDeletePrestation($request, $response, $args) {
		$idPrestation = $_POST['idPrestation'];
		$prestation = Prestation::where("idprestation",'=',$idPrestation)->first();
		if (isset($_POST['deletePrestation'])) {
			if ($_POST['deletePrestation'] == 't') {
				$contenuCoffret = ContenuCoffret::where('idPrestation', '=', $idPrestation)->delete();
			}
		}
		$prestation->delete();
	}

}
<?php

namespace MyGiftBox\controllers;

use MyGiftBox\models\Prestation;
use MyGiftBox\models\Membre;
use MyGiftBox\models\Categorie;
use MyGiftBox\models\ContenuCoffret;
use MyGiftBox\controllers\Authentication;
use MyGiftBox\controllers\BoxController;
use \Slim\Views\Twig as twig;
use MyGiftBox\views\AdminPrestationsView;

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
		if ($_SESSION['roleMember'] == 1) {
			return $this->view->render($response, 'AdminPrestationsView.html.twig', [
				'nameMember' => $_SESSION['forenameMember'],
				'roleMember' => $_SESSION['roleMember'],
			]);
		} else {
			return $this->view->render($response, 'Fail.html.twig', [
				'nameMember' => $_SESSION['forenameMember'],
				"message" => "Désolé, vous n'avez pas les droits pour venir ici :p",
				'roleMember' => $_SESSION['roleMember'],
			]);
		}
	}
	
	/**
	 * Method that displays the form for the add of a prestation
	 * @param request
	 * @param response
	 * @param args
	 */
	public function displayAddPrestation($request, $response, $args) {
		$listCategories = Categorie::select('nomCategorie')->get()->toArray();
		if ($_SESSION['roleMember'] == 1) {
			return $this->view->render($response, 'AddPrestationView.html.twig', [
				'nameMember' => $_SESSION['forenameMember'],
				'roleMember' => $_SESSION['roleMember'],
				'listCategories' => $listCategories,
			]);
		} else{
			return $this->view->render($response, 'Fail.html.twig', [
				'nameMember' => $_SESSION['forenameMember'],
				"message" => "Désolé, vous n'avez pas les droits pour venir ici :p",
				'roleMember' => $_SESSION['roleMember'],
			]);
		}
	}
	
	/**
	 * Method that checks the add of a prestation in the database
	 * @param request
	 * @param response
	 * @param args
	 */
	public function checkAddPrestation($request, $response, $args) {
		$prestation = new Prestation();
		$maxsize = 15728640;
		$error = "";
		$prestation->nomPrestation = filter_var($_POST['namePrestation'], FILTER_SANITIZE_STRING);
		$prestation->descr = $_POST['descrPrestation'];
		if ($_FILES['imgPrestation']['error'] > 0) {
			$error = "Erreur lors du transfert";
		}
		if ($_FILES['imgPrestation']['size'] > $maxsize) {
			$error = "Le fichier est trop gros";
		}
		$extensions_valides = array('jpg', 'jpeg', 'gif', 'png');
		$extension_upload = strtolower(substr(strrchr($_FILES['imgPrestation']['name'], '.'), 1));
		if (!in_array($extension_upload,$extensions_valides)) {
			$error = "Extension incorrecte";
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
		$category = Categorie::select('idCategorie', 'nomCategorie')->where('nomCategorie', '=', $_POST['categoriePrestation'])->first()->toArray();
		$prestation->idCategorie = $category['idCategorie'];
		$prestation->save();
		//return $error;
		$lastPrestation = Prestation::select('idPrestation')->where('nomPrestation', '=', $_POST['namePrestation'])->first()->toArray();
		return $lastPrestation['idPrestation'];
	}
	
	/**
	 * Method that displays the page where an admin can deactivate or reactivate a prestation
	 * @param request
	 * @param response
	 * @param args
	 */
	public function displayDeactivateReactivatePrestation($request, $response, $args) {
		$listCategories = Categorie::select('nomCategorie')->get()->toArray();
		$prestations = Prestation::all();
        $tabPrestations = array();
        for($i=0; $i<sizeof($prestations); $i++) {
            $tabPrestations[$i]['idPrestation'] = $prestations[$i]['idPrestation'];
            $tabPrestations[$i]['picture'] = $prestations[$i]['img'];
            $tabPrestations[$i]['namePrestation'] = $prestations[$i]['nomPrestation'];
            $category = $prestations[$i]->categorie()->first()->toArray();
            $tabPrestations[$i]['category'] = $category['nomCategorie'];
			$tabPrestations[$i]['price'] = $prestations[$i]['prix'];
			$tabPrestations[$i]['activate'] = $prestations[$i]['activation'];
		}
		if($_SESSION['roleMember'] == 1) {
			return $this->view->render($response, 'DeactivateReactivatePrestationView.html.twig', [
				'nameMember' => $_SESSION['forenameMember'],
				'roleMember' => $_SESSION['roleMember'],
				'tabPrestations' => $tabPrestations,
				'listCategories' => $listCategories,
			]);
		} else {
			return $this->view->render($response, 'Fail.html.twig', [
				'nameMember' => $_SESSION['forenameMember'],
				"message" => "Désolé, vous n'avez pas les droits pour venir ici :p",
			]);
		}
	}
	
	/**
	 * Method that check the deactivation or the reactivation of a prestation and modifies the prestation
	 * @param request
	 * @param response
	 * @param args
	 */
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
	
	/**
	 * Method that displays the page where an admin can delete a prestation
	 * @param request
	 * @param response
	 * @param args
	 */
	public function displayDeletePrestation($request, $response, $args) {
		$listCategories = Categorie::select('nomCategorie')->get()->toArray();
		$prestations = Prestation::all();
        $tabPrestations = array();
        for($i=0; $i<sizeof($prestations); $i++) {
            $tabPrestations[$i]['idPrestation'] = $prestations[$i]['idPrestation'];
            $tabPrestations[$i]['picture'] = $prestations[$i]['img'];
            $tabPrestations[$i]['namePrestation'] = $prestations[$i]['nomPrestation'];
            $category = $prestations[$i]->categorie()->first()->toArray();
            $tabPrestations[$i]['category'] = $category['nomCategorie'];
			$tabPrestations[$i]['price'] = $prestations[$i]['prix'];
		}
		if($_SESSION['roleMember'] == 1) {
			return $this->view->render($response, 'DeletePrestationView.html.twig', [
				'nameMember' => $_SESSION['forenameMember'],
				'roleMember' => $_SESSION['roleMember'],
				'tabPrestations' => $tabPrestations,
				'listCategories' => $listCategories,
			]);
		} else {
			return $this->view->render($response, 'Fail.html.twig', [
				'nameMember' => $_SESSION['forenameMember'],
				"message" => "Désolé, vous n'avez pas les droits pour venir ici :p",
				'roleMember' => $_SESSION['roleMember'],
			]);
		}
	}
	
	/**
	 * Method that checks the removal of a prestation from the database
	 * @param request
	 * @param response
	 * @param args
	 */
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
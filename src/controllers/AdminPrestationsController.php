<?php

namespace MyGiftBox\controllers;

use \Slim\Views\Twig as twig;
use MyGiftBox\controllers\Authentication;
use MyGiftBox\controllers\BoxController;
use MyGiftBox\views\AdminPrestationsView;
use MyGiftBox\models\Prestation;
use MyGiftBox\models\Membre;
use MyGiftBox\models\Categorie;

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
        return $this->view->render($response, 'AdminPrestationsView.html.twig', [
            'nomMembre' => $_SESSION['prenomMembre'],
			'role' => $_SESSION['roleMembre'],
        ]);
	}
	
	public function displayAddPrestation($request, $response, $args) {
        $listCategories = Categorie::select('nomCategorie')->get()->toArray();
        return $this->view->render($response, 'AddPrestationView.html.twig', [
            'nomMembre' => $_SESSION['prenomMembre'],
			'role' => $_SESSION['roleMembre'],
            'listCateg' => $listCategories,
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
    
}
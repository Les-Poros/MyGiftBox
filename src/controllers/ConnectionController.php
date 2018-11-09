<?php

namespace MyGiftBox\controllers;

use MyGiftBox\models as m;
use MyGiftBox\controllers\Authentication;
use \Slim\Views\Twig as twig;
use MyGiftBox\views\CreateAccountView;
use MyGiftBox\views\ConnexionView;

/**
 * Class ConnectionController
 */
class ConnectionController{
    
    protected $view;

	/**
	 * Constructor of the class ConnectionController
	 * @param view
	 */
    public function __construct(twig $view) {
        $this->view = $view;
    }

    /**
	 * 
	 * @param request
	 * @param response
	 * @param args
	 */
    public function displayCreateAccount($request, $response, $args) {
		return $this->view->render($response, 'CreateAccountView.html.twig', []);
    }

    /**
	 * 
	 * @param request
	 * @param response
	 * @param args
	 */
    public function displayConnection($request, $response, $args) {
		return $this->view->render($response, 'ConnexionView.html.twig', []);
    }
    
    /**
	 * 
	 */
    public function checkAccountCreation(){
        $nom = filter_var($_POST['nom'],FILTER_SANITIZE_STRING);
        $prenom = filter_var($_POST['prenom'],FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
        $mdp = filter_var($_POST['mdp'],FILTER_SANITIZE_STRING);

        $mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT, ['cost'=>12]);
        self::createMember($nom,$prenom, $mdp, $email);
        self::checkTheConnection();
    }

    /**
	 * 
	 */
    public static function checkTheConnection(){
        $email = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
        $mdp = filter_var($_POST['mdp'],FILTER_SANITIZE_STRING);

		$membre = m\Membre::where('mailMembre', '=', $email);
		if ($membre->count() != 1) {
			echo "Email invalide" ;
		} else {	
			if (password_verify($mdp, $membre->first()->passwordMembre)) {
				$membre = $membre->first();
				Authentication::instantiateSession($membre->nomMembre, $membre->prenomMembre,$membre->mailMembre,$membre->idMembre, $membre->role);
			} else {
				echo "Mot de passe invalide";
			}
		} 
    }
    
    /**
	 * 
	 * @param request
	 * @param response
	 * @param args
	 */
    public static function checkDestroySession($request, $response, $args) {
		Authentication::destroySession();
	}

    /**
	 * 
	 * @param name
	 * @param forename
	 * @param password
     * @param mail
	 */
    public static function createMember($name, $forename, $password, $mail){
        $member = new m\Membre();
        $member->nomMembre = $name;
        $member->prenomMembre = $forename;
        $member->mailMembre = $mail;
        $member->passwordMembre = $password;
        $member->role = 0;
        $member->save();

    }

    /**
	 * 
	 * @param request
	 * @param response
	 * @param args
	 */
    public function displayAccount($request, $response, $args){
        return $this->view->render($response, 'MyAccountView.html.twig', [
            'mail' => $_SESSION['mailMember'],
            'nom' => $_SESSION['nameMember'],
            'prenom' => $_SESSION['forenameMember'],
            'nomMembre' => $_SESSION['forenameMember'],
        ]);
    }

    /**
	 * 
	 */
    public function modifMember(){
        $nom = filter_var($_POST['nom'],FILTER_SANITIZE_STRING);
        $prenom = filter_var($_POST['prenom'],FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
        $mdp = filter_var($_POST['mdp'],FILTER_SANITIZE_STRING);
        $mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT, ['cost'=>12]);

        $member = m\Membre::where('mailMembre','=',$_SESSION['mailMember'])->first();
        if ($nom != "") {
            $member->nomMembre = $nom;
            $_SESSION['nameMember'] = $nom;
        }
        if ($prenom != "") {
            $member->prenomMembre = $prenom;
            $_SESSION['forenameMember'] = $prenom;
        }
        if ($email != "") {
            $member->mailMembre = $email;
            $_SESSION['mailMember'] = $email;
        }
        if ($mdp != "") {
            $member->passwordMembre = $mdp;
        }
        $member->save();
        self::checkTheConnection();
    }
    
}
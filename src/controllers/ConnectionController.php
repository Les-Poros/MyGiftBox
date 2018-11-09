<?php

namespace MyGiftBox\controllers;

use MyGiftBox\models as m;
use MyGiftBox\controllers\Authentication;
use \Slim\Views\Twig as twig;
use MyGiftBox\views\CreateAccountView;
use MyGiftBox\views\ConnectionView;

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
	 * Method that displays the form for the creation of an account
	 * @param request
	 * @param response
	 * @param args
	 */
    public function displayCreateAccount($request, $response, $args) {
		return $this->view->render($response, 'CreateAccountView.html.twig', []);
    }

    /**
	 * Method that displays the form for a connection
	 * @param request
	 * @param response
	 * @param args
	 */
    public function displayConnection($request, $response, $args) {
		return $this->view->render($response, 'ConnectionView.html.twig', []);
    }
    
    // Method that checks the creation of an account
    public function checkAccountCreation(){
        $name = filter_var($_POST['nom'],FILTER_SANITIZE_STRING);
        $forename = filter_var($_POST['prenom'],FILTER_SANITIZE_STRING);
        $mail = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
        $password = filter_var($_POST['mdp'],FILTER_SANITIZE_STRING);

        $passwordHash = password_hash($password, PASSWORD_DEFAULT, ['cost'=>12]);
        self::createMember($name, $forename, $passwordHash, $mail);
        self::checkTheConnection();
    }

    // Method that checks the connection
    public static function checkTheConnection(){
        $mail = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
        $password = filter_var($_POST['mdp'],FILTER_SANITIZE_STRING);

		$member = m\Membre::where('mailMembre', '=', $email);
		if ($member->count() != 1) {
			echo "Email invalide";
		} else {	
			if (password_verify($password, $member->first()->passwordMembre)) {
				$member = $member->first();
				Authentication::instantiateSession($member->nomMembre, $member->prenomMembre, $member->mailMembre, $member->idMembre, $member->role);
			} else {
				echo "Mot de passe invalide";
			}
		} 
    }
    
    /**
	 * Method that checks the destruction of a session
	 * @param request
	 * @param response
	 * @param args
	 */
    public static function checkDestroySession($request, $response, $args) {
		Authentication::destroySession();
	}

    /**
	 * Method that creates a member
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
	 * Method that displays the account space
	 * @param request
	 * @param response
	 * @param args
	 */
    public function displayAccount($request, $response, $args){
        return $this->view->render($response, 'MyAccountView.html.twig', [
            'mail' => $_SESSION['mailMember'],
            'name' => $_SESSION['nameMember'],
            'forename' => $_SESSION['forenameMember'],
            'nameMember' => $_SESSION['forenameMember'],
        ]);
    }

    // Method that modifies the information of a member
    public function modifMember(){
        $name = filter_var($_POST['nom'],FILTER_SANITIZE_STRING);
        $forename = filter_var($_POST['prenom'],FILTER_SANITIZE_STRING);
        $mail = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
        $password = filter_var($_POST['mdp'],FILTER_SANITIZE_STRING);
        $passwordHash= password_hash($password, PASSWORD_DEFAULT, ['cost'=>12]);

        $member = m\Membre::where('mailMembre', '=', $_SESSION['mailMember'])->first();
        if ($name != "") {
            $member->nomMembre = $name;
            $_SESSION['nameMember'] = $name;
        }
        if ($forename != "") {
            $member->prenomMembre = $forename;
            $_SESSION['forenameMember'] = $forename;
        }
        if ($mail != "") {
            $member->mailMembre = $email;
            $_SESSION['mailMember'] = $email;
        }
        if ($passwordHash != "") {
            $member->passwordMembre = $mdp;
        }
        $member->save();
        self::checkTheConnection();
    }
    
}
<?php

namespace MyGiftBox\controllers;
use MyGiftBox\models as m;
use MyGiftBox\controllers\Authentication;
use \Slim\Views\Twig as twig;
use MyGiftBox\views\CreateAccountView;
use MyGiftBox\views\ConnexionView;

class ConnectionController{
    
    public function __construct(twig $view) {
        $this->view = $view;
    }

    public function displayCreateAccount($request, $response, $args) {
		
		return $this->view->render($response, 'CreateAccountView.html.twig', []);
    }

    public function displayConnection($request, $response, $args) {
		
		return $this->view->render($response, 'ConnexionView.html.twig', []);
    }
    
    public function checkAccountCreation(){
        $nom = filter_var($_POST['nom'],FILTER_SANITIZE_STRING);
        $prenom = filter_var($_POST['prenom'],FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
        $mdp = filter_var($_POST['mdp'],FILTER_SANITIZE_STRING);

        //hash du mot de  passe
        $mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT, ['cost'=>12]);
        self::createMember($nom,$prenom, $mdp, $email);
        self::checkTheConnection();
    }

    public static function checkTheConnection(){
        $email = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
        $mdp = filter_var($_POST['mdp'],FILTER_SANITIZE_STRING);

		$membre = m\Membre::where('mailMembre', '=', $email);
		if ($membre->count() != 1) {
			echo "Email invalide" ;
		}
		else {	
			if (password_verify($mdp, $membre->first()->passwordMembre)) {
				$membre = $membre->first();
				Authentication::instantiateSession($membre->nomMembre, $membre->prenomMembre,$membre->mailMembre);
			}
			else {
				echo "Mot de passe invalide";
			}
		} 
    }
    
    public static function checkDestroySession($request, $response, $args) {
		Authentication::destroySession();
	}

    public static function createMember($nom,$prenom,$mdp,$email){
        $member = new m\Membre();
        $member->nomMembre = $nom;
        $member->prenomMembre = $prenom;
        $member->mailMembre = $email;
        $member->passwordMembre = $mdp;

        $member->save();

    }
}
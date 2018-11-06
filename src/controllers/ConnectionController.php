<?php

namespace MyGiftBox\controllers;
use MyGiftBox\models as m;
use \Slim\Views\Twig as twig;
use MyGiftBox\views\CreateAccountView;

class ConnectionController{
    
    public function __construct(twig $view) {
        $this->view = $view;
    }

    public function displayCreateAccount($request, $response, $args) {
		
		return $this->view->render($response, 'CreateAccountView.html.twig', []);
    }
    
    public function checkAccountCreation(){
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];
        $mdp = $_POST['mdp'];

        //hash du mot de  passe
        $mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT, ['cost'=>12]);
        self::createMember($nom,$prenom, $mdp, $email);
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
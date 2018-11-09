<?php

namespace MyGiftBox\controllers;

use \Slim\Views\Twig as twig;
use MyGiftBox\views\CreationBoxView;
use MyGiftBox\views\ShareBoxView;
use MyGiftBox\models\Prestation;
use MyGiftBox\models\Membre;
use MyGiftBox\models\Coffret;
use MyGiftBox\models\ContenuCoffret;

/**
 * Class BoxController
 */
class BoxController {

    protected $view;

	/**
	 * Constructor of the class HomeController
	 * @param view
	 */
    public function __construct(twig $view) {
        $this->view = $view;
    }

	/**
	 * Method that displays the home
	 * @param request
	 * @param response
	 * @param args
	 */
	public function displayCreationBox($request, $response, $args) {
		$memberName = $_SESSION['prenomMembre'];
		return $this->view->render($response, 'CreationBoxView.html.twig', [
            'nomMembre' => $memberName,
			'role' => $_SESSION['roleMembre'],
		]);

    }
    public function displayEditBox($request, $response, $args) {
		$memberName = $_SESSION['prenomMembre'];
		return $this->view->render($response, 'EditBoxView.html.twig', [
            'nomMembre' => $memberName,
		]);

    }

    public function creationBox(){
        $nameBox = filter_var($_POST['nameBox'],FILTER_SANITIZE_STRING);
        $messageBox = filter_var($_POST['messageBox'],FILTER_SANITIZE_STRING);
        $dateBox = $_POST['dateBox'];
        
        $membre= Membre::where('mailMembre','=',$_SESSION['mailMembre'])->first();
        $box = new Coffret();
        $box->nomCoffret = $nameBox;
        $box->messageCoffret = $messageBox;
        $box->dateOuvertureCoffret = $dateBox;
        $box->idMembre = $membre['idMembre'];
        $box->estCree = 1;
        $box->estOuvert = 0;
        $box->estPaye = 0;
        $box->estTransmis = 0;
        $box->hasContenuCoffret = 0;
        $box->msgRemerciement = "";
        $box->tokenCoffret = "";

        $box->save();
        return $box->idCoffret;
    }

    public static function displayBox($request, $response, $args){
            $mail = $_SESSION['mailMembre'];
			//récupère id du membre connecté
			$box= Membre::where('mailMembre', '=', $mail);
			$coffretFirst = $box->first();
			$idBox = $coffretFirst->idMembre;
            //On vérifie si le membre connecté à déjà un coffret
            $boxHaveContenu = false;
            $memberList = Coffret::select('idMembre')->get()->toArray();
            foreach($memberList as $values){
                $memberId = $values['idMembre'];
                if($idBox == $memberId ){
                    $boxHaveContenu = true;
                }
            }


            // On vérifie si il y a du contenu dans le coffret
            if($boxHaveContenu){
                $isContenuList = Coffret::select('hasContenuCoffret','nomCoffret','idCoffret','estPaye')->where('idMembre','=',$idBox)->get()->toArray();
                $listBoxesName = array();
                foreach($isContenuList as $values){
                    $isContenu = $values['hasContenuCoffret'];
                    $boxName = $values['nomCoffret'];
                    $idBox = $values['idCoffret'];
                    $isPay = $values['estPaye'];
                    if($isContenu == 0 ){
                        $imgDefault = "defaultBox.png";
                        array_push($listBoxesName,[$boxName,$imgDefault,$idBox,$isPay]);
                    }
                   else{
                        $idPrestation = ContenuCoffret::select('idPrestation')->where('idCoffret','=',$idBox)->first()->toArray();
                        $prestation = Prestation::select('img')->where('idPrestation','=',$idPrestation)->first()->toArray();
                        $imgPrestation = $prestation['img'];
                        array_push($listBoxesName,[$boxName,$imgPrestation,$idBox,$isPay]);
                   }
                }
               
                return($listBoxesName);
        }
			
    }

    public function displayEditMod($request, $response, $args){
        $memberName = $_SESSION['prenomMembre'];
        $idBox = $args['id'];
        $boxName = Coffret::select('nomCoffret')->where('idCoffret','=',$idBox)->first()->toArray();
        $boxDate = Coffret::select('dateOuvertureCoffret')->where('idCoffret','=',$idBox)->first()->toArray(); 
        $isPay = Coffret::select('estPaye')->where('idCoffret','=',$idBox)->first()->toArray();
        $isSend = Coffret::select('estTransmis')->where('idCoffret','=',$idBox)->first()->toArray();
        $isOpen = Coffret::select('estOuvert')->where('idCoffret','=',$idBox)->first()->toArray();
        $messageMember = Coffret::select('messageCoffret')->where('idCoffret','=',$idBox)->first()->toArray();

        $infoList = array();
        $priceList = array();
        $totalPrice = 0;
        $ContenuBox = ContenuCoffret::select('idPrestation')->where('idCoffret','=',$idBox)->get()->toArray();
        foreach($ContenuBox as $values){
            $quantite = ContenuCoffret::select('quantite')->where('idPrestation','=',$values)->get()->toArray();
            $priceBox= Prestation::select('prix')->where('idPrestation','=',$values)->get()->toArray();
            $img = Prestation::select('img')->where('idPrestation','=',$values)->get()->toArray();
           array_push($infoList,[$img[0]['img'],$quantite[0]['quantite']]);
           array_push($priceList,$priceBox[0]['prix']);
        }

        foreach($priceList as $values){
            $totalPrice += $values;
        }
        
        return $this->view->render($response, 'BoxView.html.twig', [
            'nomMembre' => $memberName,
            'info' => $infoList,
            'nomCoffret' => $boxName['nomCoffret'],
            'idBox' => $idBox,
            'date' => $boxDate['dateOuvertureCoffret'],
            'prix' => $totalPrice,
            'paye' => $isPay['estPaye'],
            'message' => $messageMember['messageCoffret'],
            'ouvert' => $isOpen['estOuvert'],
            'transmis' => $isSend['estTransmis'],
        ]);
    }
   

    public function checkEditBox($request, $response, $args){
        $idBox = $args['id'];
        $nameBox = filter_var($_POST['nameBox'],FILTER_SANITIZE_SPECIAL_CHARS);
        $messageBox = filter_var($_POST['messageBox'],FILTER_SANITIZE_STRING);
        $dateBox = $_POST['dateBox'];
        
        self::editBox($nameBox,$messageBox,$dateBox,$idBox);
    }

    public static function editBox($nameBox,$messageBox,$dateBox,$idBox){
        $box = Coffret::where('idCoffret','=',$idBox)->first();
        //si les champs sont vides ont laisse ceux qui sont dans la base
        if($nameBox){
            $box->nomCoffret = $nameBox;
        }
        if($messageBox){
            $box->messageCoffret = $messageBox;
        }

        if($dateBox){
            $box->dateOuvertureCoffret = $dateBox;
        }
        
        $box->save();
    }

    private static function generateToken() {
        return bin2hex(openssl_random_pseudo_bytes(16));
    }

    public function shareBox($request, $response, $args){
        $memberName = $_SESSION['prenomMembre'];
        $idBox = $args['idCoffret'];
        $box = Coffret::select('hasContenuCoffret','nomCoffret','idCoffret','tokenCoffret')->where('idCoffret','=',$args['idCoffret'])->first();
        $box = Coffret::where('idCoffret','=',$idBox)->first();
        $box->estTransmis = 1;
        $box->save();
        if( $box['tokenCoffret']=="" ){
            $token = self::generateToken();
    
            $box->tokenCoffret = $token;
            $box->save();
        }
        else{
            $token = $box['tokenCoffret'];
        }

        $url = "http://" . $_SERVER["SERVER_NAME"];

		return $this->view->render($response, 'ShareBoxView.html.twig', [
            'nomMembre' => $memberName,
            'box' => $box['nomCoffret'],
            'token' => $token,
            'url' => $url,
		]);
    }

    
    public function displayLink($request, $response, $args){
        $token = $args['token'];
        
        $date = new \DateTime();

        $box = Coffret::where('tokenCoffret','=',$token)->first();
        $dateOuvertureCoffret = new \DateTime($box['dateOuvertureCoffret']);
        $box->estOuvert = 1;
        $box->save();
        if ($date == $dateOuvertureCoffret) {
            $estOuvrable = true;
        } else if ($date > $dateOuvertureCoffret) {
            $estOuvrable = true;
        } else {
            $estOuvrable = false;
        }

        $presta = array();
        $ContenuBox = ContenuCoffret::select('idPrestation')->where('idCoffret','=',$box['idCoffret'])->get()->toArray();
        foreach($ContenuBox as $values){
            $prestation = Prestation::select('nomPrestation', 'descr', 'img')->where('idPrestation','=',$values)->first();
            $nomPrestation = $prestation['nomPrestation'];
            $descrPrestation = $prestation['descr'];
            $imgPrestation = $prestation['img'];
            $quantitePresta = ContenuCoffret::select('quantite')->where('idPrestation','=',$values)->first();
            $quantitePrestation = $quantitePresta['quantite'];
            array_push($presta,[$nomPrestation,$descrPrestation,$imgPrestation,$quantitePrestation]);
        }

        return $this->view->render($response, 'LinkBoxView.html.twig', [
            'token' => $token,
            'nomCoffret' => $box['nomCoffret'],
            'messageCoffret' => $box['messageCoffret'],
            'date' => $box['dateOuvertureCoffret'],
            'msgRemerciement' => $box['msgRemerciement'],
            'listBox' => $presta,
            'estOuvrable' => $estOuvrable,
            'nom' => $_SESSION['nomMembre'],
            'prenom' => $_SESSION['prenomMembre'],
            'mail' => $_SESSION['mailMembre'],
		]);
    }

    public function sendThanks($request, $response, $args){
        $box = Coffret::where('tokenCoffret','=',$args['token'])->first();
        $box->msgRemerciement = $_POST['msgRemerciement'];
        $box->save();
    }

    
}
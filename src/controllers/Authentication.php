<?php
namespace MyGiftBox\controllers;


class Authentication{
    public static function instantiateSession($nomMembre, $prenomMembre, $mailMembre,$idMembre){
      $_SESSION['nomMembre'] = $nomMembre;
      $_SESSION['prenomMembre'] = $prenomMembre;
      $_SESSION['mailMembre'] = $mailMembre;
      $_SESSION['idMembre'] = $idMembre;
    } 

    public static function checkConnection(){
        if (isset($_SESSION['nomMembre'])){
          return true;
        }
        else{
          return false;
        }
      }

      public static function destroySession(){
        session_destroy();
      }
}
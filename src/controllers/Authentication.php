<?php
namespace MyGiftBox\controllers;


class Authentication{
    public static function instantiateSession($nomMembre, $prenomMembre, $mailMembre){
      $_SESSION['nomMembre'] = $nomMembre;
      $_SESSION['prenomMembre'] = $prenomMembre;
      $_SESSION['mailMembre'] = $mailMembre;
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
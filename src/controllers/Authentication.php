<?php

namespace MyGiftBox\controllers;

/**
 * Class Authentication
 */
class Authentication {

  /**
   * 
   */
  public static function instantiateSession($nomMembre, $prenomMembre, $mailMembre,$idMembre, $roleMembre) {
    $_SESSION['nameMember'] = $nomMembre;
    $_SESSION['forenameMember'] = $prenomMembre;
    $_SESSION['mailMember'] = $mailMembre;
    $_SESSION['idMember'] = $idMembre;
    $_SESSION['roleMember'] = $roleMembre;
  } 

  /**
   * 
   */
  public static function checkConnection() {
      if (isset($_SESSION['nameMember'])) {
        return true;
      }
      else {
        return false;
      }
    }

  /**
   * 
   */
  public static function destroySession() {
    session_destroy();
  }

}
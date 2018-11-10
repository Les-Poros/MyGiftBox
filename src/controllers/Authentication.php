<?php

namespace MyGiftBox\controllers;

/**
 * Class Authentication
 */
class Authentication {

  /**
   * Method that instantiates a session
   * @param nameMember
   * @param forenameMember
   * @param mailMember
   * @param idMember
   * @param roleMember
   */
  public static function instantiateSession($nameMember, $forenameMember, $mailMember, $idMember, $roleMember) {
    $_SESSION['nameMember'] = $nameMember;
    $_SESSION['forenameMember'] = $forenameMember;
    $_SESSION['mailMember'] = $mailMember;
    $_SESSION['idMember'] = $idMember;
    $_SESSION['roleMember'] = $roleMember;
  } 

  // Method that checks a connection
  public static function checkConnection() {
      if (isset($_SESSION['nameMember'])) {
        return true;
      }
      else {
        return false;
      }
    }

  // Method that destroys a session
  public static function destroySession() {
    session_destroy();
  }

}
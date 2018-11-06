<?php
namespace MyGiftBox\controllers;


class Authentication{
    public static function instantiateSession($idPlayer, $pseudoPlayer){
      $_SESSION['idPlayer'] = $idPlayer;
      $_SESSION['pseudoPlayer'] = $pseudoPlayer;
      $_SESSION['role'] = 1;
    } 
}
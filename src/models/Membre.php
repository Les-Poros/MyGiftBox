<?php

namespace MyGiftBox\models;

/**
 * Class Membre
 */
class Membre extends \Illuminate\Database\Eloquent\Model {

    protected $table = 'membre';
    protected $primaryKey = 'idMembre';
    public $timestamps = false;

    public function coffret() {
        return $this->hasMany('MyGiftBox\models\Coffret', 'idCoffret');
    }

}
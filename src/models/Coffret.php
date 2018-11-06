<?php

namespace MyGiftBox\models;

/**
 * Class Coffret
 */
class Coffret extends \Illuminate\Database\Eloquent\Model {

    protected $table = 'coffret';
    protected $primaryKey = 'idCoffret';
    public $timestamps = false;

    public function membre() {
        return $this->belongsTo('MyGiftBox\models\Membre', 'idMembre');
    }

    public function contenuCoffre() {
        return $this->hasMany('MyGiftBox\models\ContenuCoffre', 'idCoffret');
    }

}
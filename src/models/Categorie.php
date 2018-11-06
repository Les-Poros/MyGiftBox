<?php

namespace MyGiftBox\models;

/**
 * Class Categorie
 */
class Categorie extends \Illuminate\Database\Eloquent\Model {

    protected $table = 'categorie';
    protected $primaryKey = 'idCategorie';
    public $timestamps = false;

    public function prestation() {
        return $this->hasMany('MyGiftBox\models\Prestation', 'idPrestation');
    }

}
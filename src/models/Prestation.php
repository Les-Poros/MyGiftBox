<?php

namespace MyGiftBox\models;

/**
 * Class Prestation
 */
class Prestation extends \Illuminate\Database\Eloquent\Model {

    protected $table = 'prestation';
    protected $primaryKey = 'idPrestation';
    public $timestamps = false;

    public function categorie() {
        return $this->belongsTo('MyGiftBox\models\Categorie', 'idCategorie');
    }

    public function contenuCoffre() {
        return $this->hasMany('MyGiftBox\models\ContenuCoffre', 'idPrestation');
    }

}
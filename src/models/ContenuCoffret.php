<?php

namespace MyGiftBox\models;

/**
 * Class ContenuCoffret
 */
class ContenuCoffret extends \Illuminate\Database\Eloquent\Model {

    protected $table = 'contenuCoffret';
    protected $primaryKey = 'idContenuCoffret';
    public $timestamps = false;

    public function coffret() {
        return $this->belongsTo('MyGiftBox\models\Coffret', 'idCoffret');
    }

    public function prestation() {
        return $this->belongsTo('MyGiftBox\models\Prestation', 'idPrestation');
    }

}
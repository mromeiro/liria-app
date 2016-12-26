<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Clients extends Model
{
   protected $table='clientes';

   protected $dates = ['data_nascimento'];

    protected $treatments;

    public function treatments()
    {
        return $this->hasMany('App\ClientTreatments', 'cliente_id');
    }
}

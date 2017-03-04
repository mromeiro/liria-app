<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Sessions extends Model
{
    protected $table = 'sessoes';

    protected $dates = ['data_sessao'];

    public $timestamps = false;

    public function getDataSessaoAttribute($value){

        if($value == null)
            return $value;

        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d/m/Y');
    }
}

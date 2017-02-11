<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sessions extends Model
{
    protected $table = 'sessoes';

    protected $dates = ['data_sessao'];

    public $timestamps = false;
}

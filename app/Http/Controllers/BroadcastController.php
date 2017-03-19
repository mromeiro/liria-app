<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LRedis;

class BroadcastController extends Controller
{

    public function sendMessage(){

        $redis = LRedis::connection();

        $redis->publish('message', 'aeee porraaaaaa');

        return response()->json([]);

    }
}

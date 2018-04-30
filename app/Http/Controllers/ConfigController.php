<?php

namespace App\Http\Controllers;

use App\Config;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ConfigController extends Controller
{
    public function getConfigs(Request $request) {

        $config = Config::all();
        $configList = array();

        foreach ($config as &$singleConfig) {
            $configList[$singleConfig->nome] = $singleConfig->valor;
        }

        return response()->json(['result' => $configList]);
    }

    static function getConfigForController() {

        $config = Config::all();
        $configList = array();

        foreach ($config as &$singleConfig) {
            $configList[$singleConfig->nome] = $singleConfig->valor;
        }

        return $configList;
    }
}

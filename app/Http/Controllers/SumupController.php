<?php

namespace App\Http\Controllers;

use App\Config;
use App\Utils\Constants;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class SumupController extends Controller
{

    static function getSumupToken($configList){

        //Get the token
        $client = new Client();
        $result = $client->post($configList[Constants::$SUMUP_TOKEN_API_URL], [
            'form_params' => [
                'grant_type' => Constants::$SUMUP_GRANT_TYPE_CLIENT_CREDENTIALS,
                'client_id' => $configList[Constants::$SUMUP_CLIENT_ID],
                'client_secret' => $configList[Constants::$SUMUP_CLIENT_SECRET],
                'scope' => Constants::$SUMUP_SCOPE_TRANSACTION_HISTORY,
            ]
        ]);

        $json_response = json_decode($result->getBody());
        return $json_response->access_token;
    }
}

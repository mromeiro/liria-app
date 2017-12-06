<?php

namespace App\Http\Controllers;

use App\Config;
use App\Utils\Constants;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class SumupController extends Controller
{

    public function authorization(Request $request) {

        $configList = ConfigController::getConfigForController();

        $client = new Client();
        $result = $client->post($configList[Constants::$SUMUP_TOKEN_API_URL], [
            'form_params' => [
                'grant_type' => Constants::$SUMUP_GRANT_TYPE_AUTHORIZATION_CODE,
                'client_id' => $configList[Constants::$SUMUP_CLIENT_ID],
                'client_secret' => $configList[Constants::$SUMUP_CLIENT_SECRET],
                'code' => $request->code,
                'redirect_uri' => $configList[Constants::$SUMUP_REDIRECT_URI],
            ]
        ]);

        $json_response = json_decode($result->getBody());

        $config = new Config();
        $config->nome = 'refresh_token';
        $config->valor = $json_response->refresh_token;
        $config->save();

        return response()->json(['result' => 'OK']);
    }

    static function getToken($configList){

        $client = new Client();
        $result = $client->post($configList[Constants::$SUMUP_TOKEN_API_URL], [
            'form_params' => [
                'grant_type' => Constants::$SUMUP_GRANT_TYPE_REFRESH_TOKEN,
                'client_id' => $configList[Constants::$SUMUP_CLIENT_ID],
                'client_secret' => $configList[Constants::$SUMUP_CLIENT_SECRET],
                'refresh_token' => $configList[Constants::$SUMUP_GRANT_TYPE_REFRESH_TOKEN],
            ]
        ]);

        /*{
            "access_token":"ACCESS_TOKEN",
            "token_type":"Bearer",
            "expires_in":3600
            "scope":"REQUEST_SCOPES"
        }*/
        $json_response = json_decode($result->getBody());

        return $json_response->access_token;
    }

    static function getTransactionDetails($configList, $transaction_id){

        $token = SumupController::getToken($configList);

        $client = new Client();
        $result = $client->post($configList[Constants::$SUMUP_TRANSACTION_HISTORY_DETAIL_API_CONFIG] . '?transaction_code=' . $transaction_id, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ]
        ]);

        return json_decode($result->getBody());
    }
}

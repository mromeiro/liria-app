<?php

namespace App\Utils;


/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/11/17
 * Time: 15:39
 */
class Constants
{

    static $SUMUP_TRANSACTION_HISTORY_API_CONFIG = 'sumup_transaction_history_api';
    static $SUMUP_TRANSACTION_HISTORY_DETAIL_API_CONFIG = 'sumup_transaction_history_detail_api';

    static $SUMUP_GRANT_TYPE_CLIENT_CREDENTIALS = 'client_credentials';
    static $SUMUP_GRANT_TYPE_AUTHORIZATION_CODE = 'authorization_code';
    static $SUMUP_GRANT_TYPE_REFRESH_TOKEN = 'refresh_token';
    static $SUMUP_CLIENT_ID= 'client_id';
    static $SUMUP_REDIRECT_URI= 'redirect_uri';
    static $SUMUP_CLIENT_SECRET = 'client_secret';
    static $SUMUP_SCOPE_TRANSACTION_HISTORY = 'transactions.history';
    static $SUMUP_TOKEN_API_URL = 'sumup_token_api';
}

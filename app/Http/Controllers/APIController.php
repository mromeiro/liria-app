<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use Hash;
use JWTAuth;
class APIController extends Controller
{

    public function login(Request $request)
    {
        $input = $request->all();
        if (!$token = JWTAuth::attempt($input)) {
            return response()->json(['result' => 'wrong email or password.']);

        }

        $user = JWTAuth::toUser($token);
        $user->token = $token;
        return response()->json(['result' => $user]);
    }

    public function get_user_details(Request $request)
    {
        $input = $request->all();
        $user = JWTAuth::toUser($input['token']);
        return response()->json(['result' => $user]);
    }

    public function loginCheck(Request $request){
        return response()->json(['result' => 'logged']);
    }

}

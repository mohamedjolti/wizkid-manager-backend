<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Validator;


class AuthController extends Controller
{
    //

    public function login(Request $request)
    {
        $rules = [
            'email' => 'email|required',
            'password' => "required",
        ];
        //message for each rule
        $messages = [
            'email.required' => "email is required",
            'email.email'    => "the format of the email should be valid",
            'password.required' => "password is required"

        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        } else {
            $validator = Validator::make($request->all(), $rules, $messages);
            if (!auth()->attempt($request->all())) {
                return response()->json("Invalid credentiels", 401);
            }

            return response()->json(['user' => auth()->user(), 'access_token' => auth()->user()->createToken('authToken')->accessToken], 201);
        }
    }
}

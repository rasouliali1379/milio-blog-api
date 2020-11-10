<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function index(Request $request){
        return response()->json([
           'user_data' => $request->user(),
            'token' => $request->user()->token()
        ]);
    }

    public function register(Request $request){

        if ($request->has(['name','email', 'password'])){

            $register = $request->only(['name','email', 'password']);

            $validator = Validator::make($register,
                [
                    'name' => 'required|min:3',
                    'email' => 'required|email|unique:users',
                    'password' => 'required|min:6'
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'wrong input',
                ]);
            } else {
                $user = new User();
                $user['name']= $register['name'];
                $user['email']= $register['email'];
                $user['password']= bcrypt($register['password']);
                $user->save();
                return $this -> login($request);
            }
        }


        return response()->json([
            'message' => 'missing mandatory parameters',
        ]);
    }

    public function login(Request $request){
        if ($request->has(['email', 'password'])){
            $login = $request->only(['email', 'password']);

            $validator = Validator::make($login,
                [
                    'email' => 'required|email',
                    'password' => 'required|min:6'
                ]
            );

            if ($validator->fails()){
                return response()->json([
                    'message' => 'Invalid Input',
                ]);
            } else {

                if(!Auth::attempt($login)){
                    return response()->json([
                        "message" => "Wrong credentials"
                    ]);
                }

                $token = Auth::user()->createToken('authToken', ['read-only'])->accessToken;

                return response()->json([
                    "user"=> Auth::user(),
                    "token"=>$token
                ]);
            }
        }

        return response()->json([
            'message' => 'login failed',
        ]);
    }

    public function logout(Request $request){
        $tokenRepository = app('Laravel\Passport\TokenRepository');

        $isLoggedOut = $tokenRepository->revokeAccessToken($request->user()->token()->id);

        if ($isLoggedOut == 1){
            return response()->json([
                'message' => 'User successfully logged out'
            ]);
        }

        return response()->json([
            'message' => 'An error occurred'
        ]);
    }
}

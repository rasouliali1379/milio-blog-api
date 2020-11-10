<?php

namespace App\Http\Controllers;

use App\Models\contact_us;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ContactUsController extends Controller
{
    public function add(Request $request){
        if ($request->has("user_id", "message")){
            $request_detail = $request->only("user_id", "message");
            $validator = Validator::make($request_detail,
                [
                    'user_id' => 'required|exists:users,id',
                    'message' => 'required|string|min:10|max:1000'
                ]
            );

            if ($validator->fails()){
                return response()->json([
                    'message' => 'Wrong input'
                ], 400);
            } else {
                $user = User::find($request_detail["user_id"])->first();
                $contact_us = new contact_us();
                $contact_us["user_id"] = $user["id"];
                $contact_us["name"] = $user["name"];
                $contact_us["email"] = $user["email"];
                $contact_us["message"] = $request_detail["message"];
                $updateStatus = $contact_us->save();

                if ($updateStatus){
                    return response()->json([
                        'message' => 'Message added successfully'
                    ], 200);
                }
            }
        } else {
            if ($request->has("email", "name", "message")){
                $request_detail = $request->only("email", "name", "message");
                $validator = Validator::make($request_detail,
                    [
                        'email' => 'required|email',
                        'name' => 'required|string|min:3|max:24',
                        'message' => 'required|string|min:10|max:1000'
                    ]
                );

                if ($validator->fails()){
                    return response()->json([
                        'message' => 'Wrong input'
                    ], 400);
                } else {
                    $contact_us = new contact_us();
                    $contact_us["email"] = $request_detail["email"];
                    $contact_us["name"] = $request_detail["name"];
                    $contact_us["message"] = $request_detail["message"];
                    $contact_us->save();

                    $updateStatus = $contact_us->save();

                    if ($updateStatus){
                        return response()->json([
                            'message' => 'Message added successfully'
                        ], 200);
                    }
                }
            }

            return response()->json([
                'message' => 'Missing mandatory arguments'
            ], 400);
        }

        return response()->json([
            'message' => 'Missing mandatory arguments'
        ], 400);
    }

    public function seen(Request $request){

        if ($request->has('id')){
            $contact_us = new contact_us();
            $contact_us["id"] = $request["id"];
            $contact_us["seen"] = true;
            $contact_us->exists = true;
            $contact_us->save();

            $updateStatus = $contact_us->save();

            if ($updateStatus){
                return response()->json([
                    'message' => 'Message updated successfully'
                ], 200);
            }
        }

        return response()->json([
            'message' => 'Missing mandatory arguments'
        ], 400);
    }

    public function all(Request $request){
        if ($request->has('seen')) {
            $messages = DB::table('contact_us')->where('seen', $request['seen'])
                ->leftJoin('users as u1', 'u1.id', '=',  'contact_us.user_id')
                ->select('contact_us.*' , 'u1.name as name', 'u1.email as email')
                ->get();

            return response()->json([
                'results' => $messages
            ], 200);
        }

        $messages = DB::table('contact_us')
            ->leftJoin('users as u1', 'u1.id', '=',  'contact_us.user_id')
            ->select('contact_us.*' , 'u1.name as name', 'u1.email as email')
            ->get();

        return response()->json([
            'results' => $messages
        ], 200);
    }
}

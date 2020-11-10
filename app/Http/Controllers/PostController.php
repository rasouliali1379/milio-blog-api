<?php

namespace App\Http\Controllers;

use App\Models\post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function add(Request $request){
        if ($request->has("title", "short_desc", "main_content", "status", "creator_ip" , "category_id" )){
            $request_detail = $request->only("title", "short_desc", "main_content", "status", "creator_ip", "category_id");
                $validator = Validator::make($request_detail,
                    [
                        'title' => 'required|string|min:3|max:100',
                        'short_desc' => 'required|min:10|max:300',
                        'main_content' => 'required|min:100',
                        'status' =>'required|string',
                        'creator_ip' => 'required|min:7|max:15',
                        'category_id' => 'exists:categories,id'
                    ]
                );

            if ($validator->fails()){
                return response()->json([
                    'message' => 'Wrong input'
                ], 400);
            } else {
                $post = new post();
                $post["title"] = $request_detail["title"];
                $post["short_desc"] = $request_detail["short_desc"];
                $post["main_content"] = $request_detail["main_content"];
                $post["status"] = $request_detail["status"];
                $post["created_by"] = $request->user()->id;
                $post["creator_ip"] = $request_detail["creator_ip"];
                $post["category_id"] = $request_detail["category_id"];
                $status = $post->save();
                if ($status){
                    return response()->json([
                        'message' => 'Post added successfully'
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'Something went wrong'
                    ], 400);
                }

            }
        }

        return response()->json([
            'message' => 'Missing mandatory arguments'
        ], 400);
    }

    public function update(Request $request){
        if ($request->has("id", "title", "short_desc", "main_content", "status", "updater_ip", "category_id" )){
            $request_detail = $request->only("id", "title", "short_desc", "main_content", "status", "updater_ip", "category_id");
                $validator = Validator::make($request_detail,
                    [
                        'id' => 'required|exists:post,id',
                        'title' => 'required|string|min:3|max:100',
                        'short_desc' => 'required|min:10|max:300',
                        'main_content' => 'required|min:100',
                        'status' =>'required|string',
                        'updater_ip' => 'required|min:7|max:15',
                        'category_id' => 'exists:categories,id'
                    ]
                );

            if ($validator->fails()){
                return response()->json([
                    'message' => 'Wrong input'
                ], 400);
            } else {
                $post = new post();
                $post["id"] = $request_detail["id"];
                $post["title"] = $request_detail["title"];
                $post["short_desc"] = $request_detail["short_desc"];
                $post["main_content"] = $request_detail["main_content"];
                $post["status"] = $request_detail["status"];
                $post["updated_by"] = $request->user()->id;
                $post["updater_ip"] = $request_detail["updater_ip"];
                $post["category_id"] = $request_detail["category_id"];
                $post-> exists = true;
                $status = $post->save();

                if ($status){
                    return response()->json([
                        'message' => 'Post added successfully'
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'Something went wrong'
                    ], 400);
                }

            }
        }

        return response()->json([
            'message' => 'Missing mandatory arguments'
        ], 400);
    }

    public function delete(Request $request){
        if ($request->has("id" , "updater_ip")){
            $request_detail = $request->only("id", "updater_ip");
            $validator = Validator::make($request_detail,
                [
                    'id' => 'required|exists:post,id',
                    'updater_ip' => 'required|min:7|max:15'
                ]
            );

            if ($validator->fails()){
                return response()->json([
                    'message' => 'Wrong input'
                ], 400);
            } else {
                $post = new post();
                $post["id"] = $request_detail["id"];
                $post["status"] = "deleted";
                $post["updater_id"] = $request->user()->id;
                $post["updater_ip"] = $request_detail["updater_ip"];
                $post-> exists = true;
                $status = $post->save();

                if ($status){
                    return response()->json([
                        'message' => 'Post deleted successfully'
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'Something went wrong'
                    ], 400);
                }

            }
        }

        return response()->json([
            'message' => 'Missing mandatory arguments'
        ], 400);
    }

    public function all(Request $request){

        if (!$request->has("status")){

            $posts = DB::table('posts')->select("id" ,"title", "short_desc")
                ->join('users as u1', 'u1.id', '=', 'posts.created_by')
                ->leftJoin('users as u2', 'u2.id', '=',  'posts.updated_by')
                ->select('posts.id', 'posts.title', 'posts.short_desc' , 'u1.name as creator_name', 'u2.name as updater_name')
                ->get();
            return response()->json([
               'results' => $posts
            ], 200);
        }

        $posts = DB::table('posts')
            ->where("status", $request["status"])
            ->join('users as u1', 'u1.id', '=', 'posts.created_by')
            ->leftJoin('users as u2', 'u2.id', '=',  'posts.updated_by')
            ->select('posts.id', 'posts.title', 'posts.short_desc' , 'u1.name as creator_name', 'u2.name as updater_name')
            ->get();
        return response()->json([
            'results' => $posts
        ], 200);
    }


    public function post(Request $request){
        $id = $request->route('id');
        $post = post::find($id);
        if ($post != null){
            $post = $post
                ->first()
                ->join('users as u1', 'u1.id', '=', 'posts.created_by')
                ->leftJoin('users as u2', 'u2.id', '=',  'posts.updated_by')
                ->select('posts.*' , 'u1.name as creator_name', 'u2.name as updater_name')
                ->get();
            return response()->json([
                'post' => $post[0]
            ], 200);
        }

        return response()->json([
            'message' => 'No post found with this id'
        ], 400);
    }
}

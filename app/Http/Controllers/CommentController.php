<?php

namespace App\Http\Controllers;

use App\Models\comment;
use App\Models\post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function add(Request $request){
        if ($request->has(['text', 'post_id', 'user_ip'])) {
            $comment_detail = $request->only(['text', 'post_id', 'user_ip']);

            $validator = Validator::make($comment_detail,
                [
                    'text' => 'required|string|min:3',
                    'post_id' => 'required',
                    'user_ip' => 'required|min:7|max:15'
                ]
            );

            if ($validator->fails()){
                return response()->json([
                    'message' => 'Wrong input'
                ], 400);
            } else {

                $comment = new comment();
                $comment['name'] = $comment_detail['text'];
                $comment['creator_ip'] = $comment_detail['post_id'];
                $comment['user_ip'] = $comment_detail['user_ip'];
                $comment['user_id'] = $request->user()->id;
                $comment['status'] = 'unconfirmed';
                $comment->save();

                return response()->json([
                    'message' => 'Comment added successfully'
                ],200);
            }
        }

        return response()->json([
            'message' => 'Missing mandatory arguments'
        ], 400);
    }

    public function delete(Request $request){
        if ($request->has(['id', 'updater_ip'])) {
            $comment_detail = $request->only(['id', 'updater_ip']);

            $validator = Validator::make($comment_detail,
                [
                    'id' => 'required',
                    'updater_ip' => 'required|min:7|max:15'
                ]
            );

            if ($validator->fails()){
                return response()->json([
                    'message' => 'Wrong input'
                ], 400);
            } else {

                $comment = new comment();
                $comment['id'] = $comment_detail['id'];
                $comment['updater_id'] = $request->user()->id;
                $comment['updater_ip'] = $comment_detail['updater_ip'];
                $comment['status'] = 'deleted';
                $comment->save();

                return response()->json([
                    'message' => 'Comment deleted successfully'
                ],200);
            }
        }

        return response()->json([
            'message' => 'Missing mandatory arguments'
        ], 400);
    }

    public function confirm(Request $request){
        if ($request->has(['id', 'updater_ip'])) {
            $comment_detail = $request->only(['id', 'updater_ip']);

            $validator = Validator::make($comment_detail,
                [
                    'id' => 'required',
                    'updater_ip' => 'required|min:7|max:15'
                ]
            );

            if ($validator->fails()){
                return response()->json([
                    'message' => 'Wrong input'
                ], 400);
            } else {

                $comment = new comment();
                $comment['id'] = $comment_detail['id'];
                $comment['updater_id'] = $request->user()->id;
                $comment['updater_ip'] = $comment_detail['updater_ip'];
                $comment['status'] = 'confirmed';
                $comment->save();

                return response()->json([
                    'message' => 'Comment confirmed successfully'
                ],200);
            }
        }
    }

    public function reply(Request $request){
        if ($request->has(['text', 'post_id', 'user_ip', 'reply_to'])) {
            $comment_detail = $request->only(['text', 'post_id', 'user_ip', 'reply_to']);

            $validator = Validator::make($comment_detail,
                [
                    'text' => 'required|string|min:3',
                    'post_id' => 'required',
                    'user_ip' => 'required|min:7|max:15',
                    'reply_to' => 'required'
                ]
            );

            if ($validator->fails()){
                return response()->json([
                    'message' => 'Wrong input'
                ], 400);
            } else {

                $comment = new comment();
                $comment['name'] = $comment_detail['text'];
                $comment['creator_ip'] = $comment_detail['post_id'];
                $comment['user_ip'] = $comment_detail['user_ip'];
                $comment['user_id'] = $request->user()->id;
                $comment['reply_to'] = $comment_detail['reply_to'];
                $comment['status'] = 'unconfirmed';
                $comment->save();

                return response()->json([
                    'message' => 'Comment added successfully'
                ],200);
            }
        }

        return response()->json([
            'message' => 'Missing mandatory arguments'
        ], 400);
    }

    public function all(Request $request){
        if ($request->has("post_id", "status")){
            $request_detail= $request->only("post_id", "status");

            $comments = DB::table('comments')->where("id", $request_detail["post_id"])
                ->join('users as u1', 'u1.id', '=', 'comments.created_by')
                ->leftJoin('users as u2', 'u2.id', '=',  'comments.updated_by')
                ->select('comments.*', 'u1.name as creator_name', 'u2.name as updater_name')
                ->get();
            return response()->json([
                'results' => $comments
            ]);
        }
        return response()->json([
            'message' => 'Missing mandatory arguments'
        ], 400);
    }

}

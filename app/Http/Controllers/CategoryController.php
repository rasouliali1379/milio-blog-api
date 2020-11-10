<?php

namespace App\Http\Controllers;

use App\Models\category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index(){
        $category = User::first();
        return response()->json([
           $category->categories
        ]);
    }

    public function add(Request $request){
        if ($request->has(['name','creator_ip'])) {

            $category_detail = $request->only(['name', 'creator_ip']);

            $validator = Validator::make($category_detail,
                [
                    'name' => 'required|min:3|unique:categories',
                    'creator_ip' => 'required|min:7|max:15',
                ]
            );

            if ($validator->fails()){
                return response()->json([
                    'message' => 'Wrong input'
                ]);
            } else {

                $category = new category();
                $category['name'] = $category_detail['name'];
                $category['creator_ip'] = $category_detail['creator_ip'];
                $category['created_by'] = $request->user()->id;
                $category->save();

                return response()->json([
                    'message' => 'Category added successfully'
                ]);
            }
        }

        return response()->json([
            'message' => 'Missing mandatory arguments'
        ]);
    }

    public function update(Request $request){
        if ($request->has(['id','name','updater_ip'])) {

            $category_detail = $request->only(['id','name', 'updater_ip']);

            $validator = Validator::make($category_detail,
                [
                    'id' => 'required|int',
                    'name' => 'required|min:3|unique:categories',
                    'updater_ip' => 'required|min:7|max:15',
                ]
            );

            if ($validator->fails()){
                return response()->json([
                    'message' => 'Wrong input'
                ]);
            } else {

                $category = new category();
                $category['id'] = $category_detail['id'];
                $category['name'] = $category_detail['name'];
                $category['updater_ip'] = $category_detail['updater_ip'];
                $category['updated_by'] = $request->user()->id;
                $category->exists = true;
                $updateStatus = $category->save();

                if ($updateStatus){
                    return response()->json([
                        'message' => 'Category updated successfully'
                    ]);
                }

                return response()->json([
                    'message' => 'Failed to update the category'
                ]);
            }
        }

        return response()->json([
            'message' => 'Missing mandatory arguments'
        ]);
    }


    public function delete(Request $request){
        if ($request->has(['id','updater_ip'])) {

            $category_detail = $request->only(['id', 'updater_ip']);

            $validator = Validator::make($category_detail,
                [
                    'id' => 'required|int',
                    'updater_ip' => 'required|min:7|max:15',
                ]
            );

            if ($validator->fails()){
                return response()->json([
                    'message' => 'Wrong input'
                ]);
            } else {

                $category = new category();
                $category['id'] = $category_detail['id'];
                $category['deleted'] = true;
                $category['updater_ip'] = $category_detail['updater_ip'];
                $category['updated_by'] = $request->user()->id;
                $category->exists = true;
                $updateStatus = $category->save();

                if ($updateStatus){
                    return response()->json([
                        'message' => 'Category deleted successfully'
                    ]);
                }

                return response()->json([
                    'message' => 'Failed to delete the category'
                ]);
            }
        }

        return response()->json([
            'message' => 'Missing mandatory arguments'
        ]);
    }

    public function all(Request $request){
        if ($request->has("deleted")){
            $request_detail= $request->only("deleted");
            if ($request_detail["deleted"] === "true"){
                $categories =   DB::table('categories')
                    ->join('users as u1', 'u1.id', '=', 'categories.created_by')
                    ->leftJoin('users as u2', 'u2.id', '=',  'categories.updated_by')
                    ->select('categories.*', 'u1.name as creator_name', 'u2.name as updater_name')
                    ->get();
                return response()->json([
                    'results' => $categories
                ]);
            } else {
                $categories = DB::table('categories')
                    ->join('users as u1', 'u1.id', '=', 'categories.created_by')
                    ->leftJoin('users as u2', 'u2.id', '=',  'categories.updated_by')
                    ->select('categories.*', 'u1.name as creator_name', 'u2.name as updater_name')
                    ->where('deleted',false)
                    ->get();
                return response()->json([
                    'results' => $categories
                ]);
            }
        } else {
            $categories =  DB::table('categories')
                ->join('users as u1', 'u1.id', '=', 'categories.created_by')
                ->leftJoin('users as u2', 'u2.id', '=',  'categories.updated_by')
                ->select('categories.*', 'u1.name as creator_name', 'u2.name as updater_name')
                ->where('deleted',false)
                ->get();
            return response()->json([
                'results' => $categories
            ]);
        }
    }
}

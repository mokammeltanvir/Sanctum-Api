<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['posts'] = Post::all();

        return response()->json([
            'success' => true,
            'message' => 'Post List',
            'data' => $data,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $ValidateUser = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($ValidateUser->fails()) {
            return response()->json([
                'success' => false,
                'message' => $ValidateUser->errors(),
            ], 401);
        }

        $img = $request->image;
        $ext = $img->getClientOriginalExtension();
        $imageName = time() . '.' . $ext;
        $img->move(public_path() . '/uploads/', $imageName);

        $post = Post::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imageName,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Post Created Successfully',
            'post' => $post,
        ], 200);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data['post'] = Post::select('id', 'title', 'description', 'image')->where('id', $id)->get();

        return response()->json([
            'success' => true,
            'message' => 'Post Details',
            'data' => $data,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $ValidateUser = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($ValidateUser->fails()) {
            return response()->json([
                'success' => false,
                'message' => $ValidateUser->errors(),
            ], 401);
        }

        $postImage = Post::select('id', 'image')->where('id', $id)->get();

        if($request->image != ''){
            $path = public_path() . '/uploads/';

            if($postImage[0]->image != '' && $postImage[0]->image != null){
                $old_file = $path . $postImage[0]->image;
                if(file_exists($old_file)){
                    unlink($old_file);
        }

        }
        $img = $request->image;
        $ext = $img->getClientOriginalExtension();
        $imageName = time() . '.' . $ext;
        $img->move(public_path() . '/uploads/', $imageName);

    }else{
        $imageName = $postImage->image;
    }

        $post = Post::where('id', $id)->update([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imageName,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Post Updated Successfully',
            'post' => $post,
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $imagePath = Post::select('image')->where('id', $id)->get();
        $filePath = public_path() . '/uploads/' . $imagePath[0]['image'];

        unlink($filePath);

        $post = Post::where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post Deleted Successfully',
            'post' => $post,
        ], 200);
    }
}

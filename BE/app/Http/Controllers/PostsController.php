<?php

namespace App\Http\Controllers;

use App\Models\Posts;

use Illuminate\Http\Request;


class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        
        $posts = Posts::with(['user', 'likes'])
            ->latest()
            ->get()
            ->map(function ($post) use ($userId) {
                return [
                    'id' => $post->id,
                    'title'=> $post->title,
                    'content' => $post->content,
                    'username' => $post->user->name,
                    // 'avatar' => $post->user->avatar,
                    'createdAt' => $post->created_at,
                    // 'tags' => $post->tags ?? [],
                    'likes_count' => $post->likes->count(),
                    // 'comments' => $post->comments->count(),
                    'is_liked' => $post->likes()->where('user_id', $userId)->exists(),
                    // ✅ This is the key - returns true if user liked the post
                ];
            });
        
        return response()->json($posts);
    }

    /**
     * Show the form for creating a new resource.
     */
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'=>'required|string|max:225',
            'content'=>'required|string',
        ]);
        $user = $request->user();

        $post = Posts::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'user_id' => $user->id
        ]);

        if(!$post){
            return response()->json([
                'message' => 'Failed to create post',
                'success' => false
            ], 500);
        }        
        return response()->json([
            'message' => 'Post created successfully',
            'success' => true,
            'data' => $post
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(posts $posts)
    {
        return response()->json($posts);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, posts $posts)
    {
        $validated = $request->validate([
            'title'=>'required|string|max:225',
            'content'=>'required|text',
        ]);
        $user = $request->user();

        if($user->id == $posts->user_id){
            $posts->title = $validated['title'];
            $posts->content = $validated['content'];
            if($posts->save()){
                return response()->json([
                    'message' => 'Post updated successfully', 
                    'success' => true,
                    'data' => $posts
                ], 200);
            }        
        }

        return response()->json([
            'message' => 'Unauthorized to update this post or failed to update', 
            'success' => false
        ], 403);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(posts $posts)
    {
        if($posts->delete()){
            return response()->json([
                'message' => 'Post deleted successfully',
                'success' => true,
                'data' => [
                    'id' => $posts->id,
                    'deleted_at' => now()
                ]
            ], 200);
        }
        
        return response()->json([
            'message' => 'Failed to delete post',
            'success' => false
        ], 500);
    }

    public function likes(posts $post,Request $req){
        $user = $req->user();

        $alreadyliked = $user->likedPosts()->where('post_id',$post->id)->exists();

        if (!$alreadyliked) {
            $user->likedPosts()->attach($post->id);
            $post->likes_count = $post->likes()->count();
            $post->save();
            $isLiked = true;
            $message = 'post Liked succesfuly';
        }else{
            $user->likedPosts()->detach($post->id);
            $post->likes_count = $post->likes()->count();
            $post->save();
            $isLiked = false;
            $message = 'post Liked removed succesfuly';
        }
        return response()->json([
            'success'=> $isLiked,
            'message'=> $message,
            'this_post_likes'=>$post->likes()->count()
        ]);

    }

}

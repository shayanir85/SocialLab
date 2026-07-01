<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\Reels;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class ReelsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Reels::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'video' => 'required|mimes:mp4,avi,mov,wmv|max:102400',
            'caption' => 'required|string|max:1000',
        ]);

        if(!$request->hasFile('video')){
            return response()->json([
                'message' => 'video is missing'
            ]);
            }
            try{
            $video = $request->file('video');
            $extension = $video->getClientOriginalExtension();
            $filename = time() . '_' . Str::uuid().'.'.$extension;

            $path = $video->storeAs('videos', $filename, 'public');

            $user = $request->user();

            $reels = Reels::create([
                'caption' => $validated['caption'],
                'video_url' =>  asset('storage/' . $path),
                'user_id'=> $user->id
            ]);

                return response()->json([
                    'data'=>$reels,
                    'message' => 'reels created'
                ],201);
            }catch(\Exception $error){
                return response()->json([
                    'Upload failed: ' . $error->getMessage(),
                    'message' => 'failed'
                ],500);
            }
    }

    /**
     * Display the specified resource.
     */
    public function show(Reels $reels)
    {
        return response()->json($reels);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reels $reels)
    {
        $validated = $request->validate([
            'video' => 'required|mimes:mp4,avi,mov,wmv|max:102400',
            'caption' => 'required|string|max:1000',
        ]);

        $user = $request->user();

        if($user->id !== $reels->user_id){
            return response()->json([
                'message' => 'Unauthorized to update this reel',
                'success' => false
            ], 403);
        }

        try{

        $reels->caption = $validated['caption'];

        if($request->hasFile('video')){
            $oldVideoPath = str_replace('/storage/', '', $reels->video_url);
            if(Storage::disk('public')->exists($oldVideoPath)){
                Storage::disk('public')->delete($oldVideoPath);
            }

            $video = $request->file('video');
            $extension = $video->getClientOriginalExtension();
            $filename = time() . '_' . Str::uuid() . '.' . $extension;
            $path = $video->storeAs('videos', $filename, 'public');
            $reels->video_url =  asset('storage/' . $path);
        }

        $reels->save();

        return response()->json([
            'data' => $reels,
            'message' => 'Reel updated successfully',
            'success' => true
        ], 200);
        }catch(\Exception $erorr){
            return response()->json([
                'data' => $erorr,
                'message' => 'Reel update failed',
                'success' => false
            ], 500);

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reels $reels)
    {
        $videoPath = str_replace('/storage', '', $reels->video_url);
        if($reels->delete()){
            if(Storage::disk('public')->exists($videoPath)){
                Storage::disk('public')->delete($videoPath);
            }
            return response()->json([
                'message' => 'reels deleted successfully',
                'success' => true,
                'data' => [
                    'id' => $reels->id,
                    'deleted_at' => now()
                ]
            ], 200);
        };

        return response()->json([
            'message' => 'Failed to delete Reel',
            'success' => false
        ], 500);
    }
    public function likes(Reels $reel,Request $req){
        $user = $req->user();

        $alreadyliked = $user->likedPosts()->where('post_id',$reel->id)->exists();

        if (!$alreadyliked) {
            $user->likedreels()->attach($reel->id);
            $reel->likes_count = $reel->likes()->count();
            $reel->save();
            $isLiked = true;
            $message = 'reel Liked succesfuly';
        }else{
            $user->likedreels()->detach($reel->id);
            $reel->likes_count = $reel->likes()->count();
            $reel->save();
            $isLiked = false;
            $message = 'reel Liked removed succesfuly';
        }
        return response()->json([
            'success'=> $isLiked,
            'message'=> $message,
            'this_reel_likes'=>$reel->likes()->count()
        ]);

    }
}

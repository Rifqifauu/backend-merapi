<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ForumPost;

class ForumPostController extends Controller
{
    // GET semua post beserta reply
   public function index()
{
    $posts = ForumPost::with(['user', 'all_replies.user' => function($query) {
        $query->orderBy('created_at', 'desc'); // balasan terbaru di atas
    }])
    ->whereNull('parent_id')
    ->orderBy('created_at', 'desc') // post terbaru di atas
    ->get();

    return response()->json($posts);
}


    // POST post baru (butuh auth)
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $post = ForumPost::create([
            'user_id' => $request->user()->id,
            'content' => $request->content,
            'parent_id' => null,
        ]);

        return response()->json($post, 201);
    }

    // POST reply ke post tertentu (butuh auth)
    public function reply(Request $request, ForumPost $post)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $reply = ForumPost::create([
            'user_id' => $request->user()->id,
            'content' => $request->content,
            'parent_id' => $post->id,
        ]);

        return response()->json($reply, 201);
    }
    public function destroy($id)
{
    $forum = ForumPost::find($id);

    if (!$forum) {
        return response()->json(['message' => 'Post not found'], 404);
    }

    // Cek apakah user yang login pemilik post atau admin
    $user = auth()->user();
    if ($user->id !== $forum->user_id && !$user->is_admin) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $forum->delete();

    return response()->json(['message' => 'Post deleted successfully'], 200);
}

public function update(Request $request, ForumPost $forum)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $user = $request->user();

        // Hanya pemilik atau admin yang boleh update
        if ($user->id !== $forum->user_id && !$user->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $forum->content = $request->content;
        $forum->save();

        return response()->json([
            'message' => 'Post updated successfully',
            'data' => $forum,
        ]);
    }

}

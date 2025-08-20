<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ForumPost;

class ForumPostController extends Controller
{
    // GET semua post beserta reply
    public function index()
    {
        $posts = ForumPost::with('all_replies.user')->whereNull('parent_id')->get();
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
}

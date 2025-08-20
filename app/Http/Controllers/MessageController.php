<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        // ambil semua pesan yang dikirim atau diterima oleh user ini
        $userId = Auth::id();

        $messages = Message::where('from_user_id', $userId)
            ->orWhere('to_user_id', $userId)
            ->with(['fromUser', 'toUser'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function store(Request $request)
    {
        $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        $message = Message::create([
            'from_user_id' => Auth::id(),
            'to_user_id' => $request->to_user_id,
            'content' => $request->content,
        ]);

        return response()->json($message, 201);
    }
}

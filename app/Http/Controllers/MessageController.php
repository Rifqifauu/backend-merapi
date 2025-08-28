<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;

class MessageController extends Controller
{
    // Kirim pesan
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'to_admin' => 'required|boolean'
        ]);

        $message = Message::create([
            'user_id' => $request->user()->id,
            'content' => $request->content,
            'to_admin' => $request->to_admin,
        ]);

        return response()->json([
            'message' => 'Pesan berhasil dikirim',
            'data' => $message
        ]);
    }

    // Ambil pesan sesuai penerima
 // Ambil semua pesan tanpa filter
public function index(Request $request)
{
    $messages = Message::with('user')
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json($messages);
}


 public function markAsRead($id)
    {
        $message = Message::find($id);

        if (!$message) {
            return response()->json(['message' => 'Pesan tidak ditemukan'], 404);
        }

        // Hanya bisa mark as read jika pesan ditujukan ke admin
        if ($message->to_admin != 1) {
            return response()->json(['message' => 'Hanya pesan masuk yang bisa diubah statusnya'], 403);
        }

        $message->status = 'read';
        $message->save();

        return response()->json(['message' => 'Pesan telah dibaca']);
    }
}

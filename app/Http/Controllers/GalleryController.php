<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        return response()->json(Gallery::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:image,video',
    'file' => 'required|file|mimes:jpeg,jpg,png,gif,mp4,avi,quicktime|max:102400' // tambahkan quicktime
        ]);

        // Simpan file ke storage/app/public/gallery
        $path = $request->file('file')->store('gallery', 'public');

        $gallery = Gallery::create([
            'title' => $request->title,
            'type' => $request->type,
            'file_path' => $path,
        ]);

        return response()->json($gallery, 201);
    }

    public function destroy(Gallery $gallery)
    {
        // Hapus file dari storage
        if ($gallery->file_path && Storage::disk('public')->exists($gallery->file_path)) {
            Storage::disk('public')->delete($gallery->file_path);
        }

        $gallery->delete();

        return response()->json(null, 204);
    }
}

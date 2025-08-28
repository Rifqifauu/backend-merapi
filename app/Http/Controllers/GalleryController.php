<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        return response()->json(Gallery::orderBy('created_at','desc')->get());
    }
public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'type'  => 'required|in:image,video',
        'file'  => 'required|file|mimes:jpeg,jpg,png,gif,mp4,avi,quicktime|max:102400',
    ]);

    if ($request->hasFile('file')) {
        $file     = $request->file('file');
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();

        // pakai path absolut langsung ke public_html
        $destination = base_path('../public_html/storage/gallery');

        if (!file_exists($destination)) {
            mkdir($destination, 0775, true);
        }

        $file->move($destination, $filename);

        $gallery = Gallery::create([
            'title'     => $request->title,
            'type'      => $request->type,
            'file_path' => 'gallery/' . $filename,
        ]);

        return response()->json($gallery, 201);
    }

    return response()->json(['error' => 'No file uploaded'], 400);
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


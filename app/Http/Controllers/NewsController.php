<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;

class NewsController extends Controller
{
    public function index()
    {
        return response()->json(News::orderBy('created_at','desc')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'media_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['title', 'content']);

        if ($request->hasFile('media_path')) {
            $file = $request->file('media_path');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            // Path absolut ke public_html/storage/news
            $destination = base_path('../public_html/storage/news');

            if (!file_exists($destination)) {
                mkdir($destination, 0775, true);
            }

            $file->move($destination, $filename);

            // Simpan path relatif ke DB
            $data['media_path'] = 'news/' . $filename;
        }

        $news = News::create($data);

        return response()->json($news, 201);
    }

    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'media_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['title', 'content']);

        if ($request->hasFile('media_path')) {
            // Hapus file lama jika ada
            if ($news->media_path && file_exists(base_path('../public_html/storage/'.$news->media_path))) {
                unlink(base_path('../public_html/storage/'.$news->media_path));
            }

            $file = $request->file('media_path');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            $destination = base_path('../public_html/storage/news');
            if (!file_exists($destination)) {
                mkdir($destination, 0775, true);
            }

            $file->move($destination, $filename);
            $data['media_path'] = 'news/' . $filename;
        }

        $news->update($data);

        return response()->json($news);
    }

    public function destroy($id)
    {
        $news = News::findOrFail($id);

        if ($news->media_path && file_exists(base_path('../public_html/storage/'.$news->media_path))) {
            unlink(base_path('../public_html/storage/'.$news->media_path));
        }

        $news->delete();

        return response()->json(['message' => 'News deleted successfully']);
    }
}

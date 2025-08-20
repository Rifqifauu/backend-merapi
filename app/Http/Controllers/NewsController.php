<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;

class NewsController extends Controller
{
    public function index()
    {
        return response()->json(News::all());
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
            $data['media_path'] = $request->file('media_path')->store('news', 'public');
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
            if ($news->media_path) {
                \Storage::disk('public')->delete($news->media_path);
            }
            $data['media_path'] = $request->file('media_path')->store('news', 'public');
        }

        $news->update($data);

        return response()->json($news);
    }

    public function destroy($id)
    {
        $news = News::findOrFail($id);
        if ($news->media_path) {
            \Storage::disk('public')->delete($news->media_path);
        }
        $news->delete();

        return response()->json(['message' => 'News deleted successfully']);
    }
}

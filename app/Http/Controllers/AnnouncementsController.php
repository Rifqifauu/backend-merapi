<?php

namespace App\Http\Controllers;

use App\Models\Announcements;
use Illuminate\Http\Request;

class AnnouncementsController extends Controller
{
    // Tampilkan semua pengumuman
    public function index()
    {
        return response()->json(Announcements::all());
    }

    // Tampilkan pengumuman tertentu
    public function show($id)
    {
        $announcement = Announcements::find($id);
        if (!$announcement) {
            return response()->json(['message' => 'Pengumuman tidak ditemukan'], 404);
        }
        return response()->json($announcement);
    }

    // Simpan pengumuman baru
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $announcement = Announcements::create($request->all());
        return response()->json($announcement, 201);
    }

    // Update pengumuman
    public function update(Request $request, $id)
    {
        $announcement = Announcements::find($id);
        if (!$announcement) {
            return response()->json(['message' => 'Pengumuman tidak ditemukan'], 404);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
        ]);

        $announcement->update($request->all());
        return response()->json($announcement);
    }

    // Hapus pengumuman
    public function destroy($id)
    {
        $announcement = Announcements::find($id);
        if (!$announcement) {
            return response()->json(['message' => 'Pengumuman tidak ditemukan'], 404);
        }

        $announcement->delete();
        return response()->json(['message' => 'Pengumuman berhasil dihapus']);
    }
}

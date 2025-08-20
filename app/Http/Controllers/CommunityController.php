<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Community;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommunityController extends Controller
{
    // List all communities
    public function index()
    {
        return Community::all();
    }

    // Store new community
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:4096',
        ]);

        $data = $request->only(['name', 'instagram', 'tiktok', 'twitter', 'whatsapp', 'facebook']);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('communities', 'public');
        }

        $community = Community::create($data);

        return response()->json($community, 201);
    }

    // Delete community
    public function destroy($id)
    {
        $community = Community::findOrFail($id);

        if ($community->logo) {
            Storage::disk('public')->delete($community->logo);
        }

        $community->delete();

        return response()->json(['message' => 'Community deleted successfully']);
    }
}

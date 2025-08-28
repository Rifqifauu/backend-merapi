<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Community;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    // GET semua community
  public function index()
{
    $communities = Community::inRandomOrder()->get();
    return response()->json($communities);
}


    // POST tambah community
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'tiktok'    => 'nullable|string|max:255',
            'twitter'   => 'nullable|string|max:255',
            'whatsapp'  => 'nullable|string|max:255',
            'facebook'  => 'nullable|string|max:255',
            'logo'      => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Simpan langsung ke public_html/storage/communities (sama seperti gallery)
            $destination = base_path('../public_html/storage/communities');
            if (!file_exists($destination)) {
                mkdir($destination, 0775, true);
            }
            
            $file->move($destination, $filename);
            $logoPath = 'communities/' . $filename;
        }

        $community = Community::create([
            'name'      => $request->name,
            'instagram' => $request->instagram,
            'tiktok'    => $request->tiktok,
            'twitter'   => $request->twitter,
            'whatsapp'  => $request->whatsapp,
            'facebook'  => $request->facebook,
            'logo'      => $logoPath,
        ]);

        return response()->json([
            'message' => 'Community created successfully',
            'data'    => $community
        ], 201);
    }

    // GET detail community
    public function show($id)
    {
        $community = Community::findOrFail($id);
        return response()->json($community);
    }

    // PUT update community
    public function update(Request $request, $id)
    {
        $community = Community::findOrFail($id);
        
        $request->validate([
            'name'      => 'required|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'tiktok'    => 'nullable|string|max:255',
            'twitter'   => 'nullable|string|max:255',
            'whatsapp'  => 'nullable|string|max:255',
            'facebook'  => 'nullable|string|max:255',
            'logo'      => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);

        $logoPath = $community->logo;
        
        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($community->logo) {
                $oldFilePath = base_path('../public_html/storage/' . $community->logo);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }
            
            $file = $request->file('logo');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Simpan logo baru
            $destination = base_path('../public_html/storage/communities');
            if (!file_exists($destination)) {
                mkdir($destination, 0775, true);
            }
            
            $file->move($destination, $filename);
            $logoPath = 'communities/' . $filename;
        }

        $community->update([
            'name'      => $request->name,
            'instagram' => $request->instagram,
            'tiktok'    => $request->tiktok,
            'twitter'   => $request->twitter,
            'whatsapp'  => $request->whatsapp,
            'facebook'  => $request->facebook,
            'logo'      => $logoPath,
        ]);

        return response()->json([
            'message' => 'Community updated successfully',
            'data'    => $community
        ]);
    }

    // DELETE community
    public function destroy($id)
    {
        $community = Community::findOrFail($id);
        
        // Hapus file logo jika ada
        if ($community->logo) {
            $filePath = base_path('../public_html/storage/' . $community->logo);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        
        $community->delete();
        
        return response()->json([
            'message' => 'Community deleted successfully'
        ]);
    }
}
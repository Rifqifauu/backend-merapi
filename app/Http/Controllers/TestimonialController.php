<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class TestimonialController extends Controller
{
    // Ambil semua testimoni
    public function index()
    {
        try {
            $testimonials = Testimonial::latest()->get();

            return response()->json($testimonials);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil testimoni: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil data testimoni'
            ], 500);
        }
    }

    // Simpan testimoni baru
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'photo' => 'nullable|file|mimes:jpeg,jpg,png,gif|max:2048',
            'testimonial' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $photoPath = null;

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $destination = base_path('../public_html/storage/testimonials');

                if (!file_exists($destination)) {
                    mkdir($destination, 0775, true);
                }

                $file->move($destination, $filename);
                $photoPath = 'testimonials/' . $filename;
            }

            $testimonial = Testimonial::create([
                'name' => $request->name,
                'photo' => $photoPath,
                'testimonial' => $request->testimonial,
                'rating' => (int) $request->rating,
            ]);

            return response()->json([
                'message' => 'Testimoni berhasil ditambahkan',
                'data' => $testimonial
            ], 201);

        } catch (\Exception $e) {
            if (isset($photoPath) && $photoPath) {
                $fullPath = base_path('../public_html/storage/' . $photoPath);
                if (file_exists($fullPath)) unlink($fullPath);
            }

            Log::error('Gagal simpan testimoni: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan testimoni'
            ], 500);
        }
    }

    // Update testimoni
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'photo' => 'nullable|file|mimes:jpeg,jpg,png,gif|max:2048',
            'testimonial' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $testimonial = Testimonial::findOrFail($id);

            if ($request->hasFile('photo')) {
                // Hapus foto lama jika ada
                if ($testimonial->photo) {
                    $oldPath = base_path('../public_html/storage/' . $testimonial->photo);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                $file = $request->file('photo');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $destination = base_path('../public_html/storage/testimonials');

                if (!file_exists($destination)) {
                    mkdir($destination, 0775, true);
                }

                $file->move($destination, $filename);
                $testimonial->photo = 'testimonials/' . $filename;
            }

            $testimonial->name = $request->name;
            $testimonial->testimonial = $request->testimonial;
            $testimonial->rating = (int) $request->rating;
            $testimonial->save();

            return response()->json([
                'message' => 'Testimoni berhasil diperbarui',
                'data' => $testimonial
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal update testimoni: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal memperbarui testimoni'
            ], 500);
        }
    }

    // Hapus testimoni
    public function destroy($id)
    {
        try {
            $testimonial = Testimonial::findOrFail($id);

            if ($testimonial->photo) {
                $photoPath = base_path('../public_html/storage/' . $testimonial->photo);
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }
            }

            $testimonial->delete();

            return response()->json([
                'message' => 'Testimoni berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal hapus testimoni: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menghapus testimoni'
            ], 500);
        }
    }
}

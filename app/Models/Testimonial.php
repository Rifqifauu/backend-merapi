<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $table = 'testimonials';

    protected $fillable = [
        'name',
        'photo',
        'testimonial', 
        'rating'
    ];

    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];


    // Boot method untuk auto-delete file saat model dihapus
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($testimonial) {
            if ($testimonial->photo) {
                $photoPath = base_path('../public_html/storage/' . $testimonial->photo);
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }
            }
        });
    }
}
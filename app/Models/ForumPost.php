<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumPost extends Model
{
    protected $fillable = [
        'user_id',
        'content',
        'parent_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi 1-level replies
    public function replies()
    {
        return $this->hasMany(ForumPost::class, 'parent_id')->with('user', 'replies');
    }

    // Recursive replies
   public function all_replies()
{
    return $this->hasMany(ForumPost::class, 'parent_id')->with('all_replies', 'user');
}

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $table = 'galleries';
       protected $fillable = ['title', 'type', 'file_path'];

}

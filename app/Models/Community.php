<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    protected $table = 'communities';

    protected $fillable = [
 'name',
    'instagram',
    'tiktok',
    'twitter',
    'whatsapp',
    'facebook',
    'logo',
      ];
}

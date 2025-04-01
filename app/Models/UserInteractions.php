<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class UserInteractions extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'movie_id',
        'interaction_type',
        'timestamp',
        'user_metadata',
        'movie_metadata',
    ];
}

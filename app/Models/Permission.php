<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    // Explicitly set table name based on your schema
    protected $table = 'permission';

    protected $fillable = [
        'attendence_id',
        'access',
        'update_info',
        'create_by',
        'createdate',
    ];

    public $timestamps = true;
}
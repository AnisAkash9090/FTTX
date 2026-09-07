<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RollManagement extends Model
{
    use HasFactory;

    // Explicitly set table name based on your schema
    protected $table = 'rollmanagement';

    protected $fillable = [
        'name',
        'identity_permission',
        'createby',
        'updateby',
        'createtime',
        'updatetime',
    ];

    public $timestamps = true;
}
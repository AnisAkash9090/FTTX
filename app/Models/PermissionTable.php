<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionTable extends Model
{
    use HasFactory;

    protected $table = 'permission_table';

    protected $fillable = [
        'permission',
        'details',
        'url',
    ];
}
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Userlist extends Model
{
    protected $table = 'userlists'; // VERY IMPORTANT

    protected $fillable = [
        'name',
        'address',
        'attendece_id',
        'img',
        'sts',
        'createinfo',
        'email',
        'password'
    ];
}

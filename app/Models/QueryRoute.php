<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueryRoute extends Model
{
    use HasFactory;

    // Explicitly define the table name since it doesn't follow Laravel's plural convention
    protected $table = 'query_route';

    // Allow these fields to be mass-assignable
    protected $fillable = [
        'brand',
        'type',
        'route',
    ];

    /**
     * NOTE: By default, Laravel expects `created_at` and `updated_at` columns in your table.
     * If your `query_route` table DOES NOT have these timestamp columns, 
     * uncomment the line below to prevent errors.
     */
    // public $timestamps = false;
}
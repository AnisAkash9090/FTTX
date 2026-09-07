<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class olt_oid extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'olt_oid';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'assign_for',
        'port_names',
        'tx_values',
        'alterTX',
        'rx_values',
        'up_value',
        'down_values',
        'sts',
        'lcv',
        'sn_values',
        'reason',
        'onumod',
        'mac_get',
        'onu_dist',
        'onuvendor',
        'router_mac',
    ];
}
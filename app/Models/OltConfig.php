<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OltConfig extends Model
{
    protected $table = 'olt_information';

    protected $fillable = [
        'user_name',
        'olt_name',
        'olt_ip',
        'olt_community',
        'useradmin',
        'pass',
        'olt_type',
        'sts',
        'type',
        'olt_brand',
        'createinfo',
        'txrxcmd',
        'typeconnection',
        'port',
        'snmpsts',
        'sshTElnetsts',
        'details_snmp',
        'details_sshtelnet'
    ];

    // Relationship with OltInformation table
    public function onuData()
    {
        return $this->hasMany(OltInformation::class, 'sys_gen_id', 'id');
    }
}

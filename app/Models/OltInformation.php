<?php

namespace App\Models;
use App\Models\OltConfig;
use Illuminate\Database\Eloquent\Model;


class OltInformation extends Model
{
    // Tells Laravel to use this specific table name
    protected $table = 'oltdatatablepresent';

    // List all columns that are allowed to be inserted/updated
    protected $fillable = [
        'sn', 
        'sys_sn', 
        'sys_mac', 
        'sys_port', 
        'sys_tx', 
        'sys_rx', 
        'upload', 
        'download', 
        'sys_vendor', 
        'sys_model', 
        'sys_device', 
        'sys_distance', 
        'sys_timeTicks', 
        'sys_lastChange', 
        'sys_from', 
        'sys_sts', 
        'reason', 
        'sys_gen_id', 
        'sync_time'
    ];
   
  public function config(){
    return $this->belongs(OltConfig::class,'sys_gen_id');
  }
}
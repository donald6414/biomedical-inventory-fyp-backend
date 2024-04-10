<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintananceReport extends Model
{
    use HasFactory;

    //Relationship between Maintenance Report and Scheduled Maintenance
    public function schedule_maintenance(){
        return $this->belongsTo(ScheduleMaintanance::class, 'schedule_maintanance_id','id');
    }

    //Relationship between Equipments and Maintenance Report
    public function equipment(){
        return $this->belongsTo(Equipment::class, 'equipment_id','id');
    }
}

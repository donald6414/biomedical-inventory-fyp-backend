<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleMaintanance extends Model
{
    use HasFactory;

    //Relationship between Equipments and Scheduled Maintenance
    public function equipment(){
        return $this->belongsTo(Equipment::class, 'equipment_id','id');
    }

    //Relationship between Scheduled Maintenance and Maintenance Report
    public function maintenance_report(){
        return $this->hasMany(MaintananceReport::class, 'schedule_maintanance_id','id');
    }
}

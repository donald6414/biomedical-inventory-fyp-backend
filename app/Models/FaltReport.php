<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaltReport extends Model
{
    use HasFactory;

    //Relationship between Falt Report and Equipments
    public function user(){
        return $this->belongsTo(User::class, 'user_id','id');
    }

    //Relationship between Falt Report and Equipments
    public function equipment(){
        return $this->belongsTo(Equipment::class, 'equipment_id','id');
    }

    //Relationship between Falt Report and Equipments
    public function falt_report_maintenance(){
        return $this->hasMany(FaltReportMaintanance::class, 'falt_report_id','id');
    }
}

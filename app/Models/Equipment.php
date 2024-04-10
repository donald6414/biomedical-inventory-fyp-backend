<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    public function latestScheduleMaintenance()
    {
        return $this->hasOne(ScheduleMaintanance::class)
            ->latestOfMany(); // Use latestOfMany() to get the latest related record
    }

    //Relationship between Equipment and User
    public function user(){
        return $this->belongsTo(User::class, 'user_id','id');
    }

    //Relationship between Equipments and Departments
    public function department(){
        return $this->belongsTo(Department::class, 'department_id','id');
    }

    //Relationship between Equipments and Scheduled Maintenance
    public function schedule_maintenace(){
        return $this->hasMany(ScheduleMaintanance::class, 'equipment_id','id');
    }

    //Relationship between Falt Report and Equipments
    public function falt_report(){
        return $this->hasMany(FaltReport::class, 'equipment_id','id');
    }
}

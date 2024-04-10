<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaltReportMaintanance extends Model
{
    use HasFactory;

    //Relationship between User and Falt report maintenance
    public function user(){
        return $this->belongsTo(User::class, 'user_id','id');
    }

    //Relationship between Falt Report and Falt report maintenance
    public function falt_report(){
        return $this->belongsTo(FaltReport::class, 'falt_report_id','id');
    }
}

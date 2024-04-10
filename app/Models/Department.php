<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    //Relationship between Departments and Equipments
    public function equipments(){
        return $this->hasMany(Equipment::class, 'department_id','id');
    }
}

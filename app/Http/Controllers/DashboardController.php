<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Equipment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function load_dashboard(){
        $departments = Department::count();

        $equipments = Equipment::count();

        $responseData = [
            [
                'value'=>$departments,
                'title'=>"Total Departments",
                'cash'=>false
            ],
            [
                'value'=>$equipments,
                'title'=>"Total Equipments",
                'cash'=>false
            ]
        ];

        return response()->json($responseData);
    }
}

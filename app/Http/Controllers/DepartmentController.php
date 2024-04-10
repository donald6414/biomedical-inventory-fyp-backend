<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function save_new_department(Request $request){
        $data = request()->validate([
            'name' => 'required|string'
        ]);

        $department = new Department();
        $department->name = $request->name;
        $department->save();

        return response()->json([200,"Okay"]);
    }

    public function update_department(Request $request, $id){
        $data = request()->validate([
            'name' => 'required|string'
        ]);

        $department = Department::findOrFail($id);
        $department->name = $request->name;
        $department->save();

        return response()->json([200,"Okay"]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Equipment;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EquipmentsController extends Controller
{
    public function get_departments(){
        $departments = Department::all();
        return response()->json($departments);
    }

    public function save_new_equipment(Request $request){
        $data = request()->validate([
            'name' => 'required|string',
            'serial_number' => 'required',
            'number_of_days' => 'required',
            'department' => 'required',
        ]);

        $equipment = new Equipment();
        $equipment->user_id = auth()->user()->id;
        $equipment->department_id = $request->department;
        $equipment->serial_no = $request->serial_number;
        $equipment->name = $request->name;
        $equipment->schedule = $request->number_of_days;

        $qrname = uniqid() . "." . "png";
        // path
         $path = "qrcode/".$qrname;

         //Generate QR Code
         $qrcode = QrCode::format('png')->size(250)->generate(
             $qrname
         );
         file_put_contents($path, $qrcode);
         $equipment->qr_path = $path;
         $equipment->qr_id = $qrname;
         $equipment->save();

         return response()->json([200,"Okay"]);
    }

    public function get_equipments(){
        $equipments = Equipment::with('department')->get();
        return response()->json($equipments);
    }

    public function update_equipment(Request $request, $id){
        $data = request()->validate([
            'name' => 'required|string',
            'serial_number' => 'required',
            'number_of_days' => 'required',
            'department' => 'required',
        ]);

        $equipment = Equipment::findOrFail($id);
        $equipment->department_id = $request->department;
        $equipment->serial_no = $request->serial_number;
        $equipment->name = $request->name;
        $equipment->schedule = $request->number_of_days;
        $equipment->save();

        return response()->json([200,"Okay!"]);
    }

    public function download_qrcode($id){
        $equipment = Equipment::findOrFail($id);

        // Retrieve the image file from storage (change the path accordingly)
        $filePath = public_path($equipment->qr_path);


        // Return the file as a response
        //return response()->download($filePath);

        // Check if the file exists
        if (file_exists($filePath)) {
            // Return the file as a response
            return response()->download($filePath);
        } else {
            // Return error response if the file does not exist
            return response()->json(['error' => 'Image not found'], 404);
        }
    }
}

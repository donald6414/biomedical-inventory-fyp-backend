<?php

namespace App\Http\Controllers;

use App\Mail\ScheduledMaintenance;
use App\Mail\FaultReport;
use App\Models\Equipment;
use App\Models\FaltReport;
use App\Models\FaltReportMaintenance;
use App\Models\MaintananceReport;
use App\Models\ScheduleMaintanance;
use App\Http\Controllers\SendMessageController;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;

class ProcessController extends Controller
{
    public function check_schedule_maintenance(){
        $sendMessageController = new SendMessageController();
        $equipments = Equipment::with('latestScheduleMaintenance','department')->get();
        Log::info($equipments);

        for ($i = 0; $i < count($equipments); $i++){
            if ($equipments[$i]->latestScheduleMaintenance){
                $today = Carbon::now();
                $last_maintenance = $equipments[$i]->latestScheduleMaintenance->date;

                $days_difference = $today->diff(new \DateTime($last_maintenance));
                Log::info("Days Difference: ". $days_difference->days);

                $days_to_next_schedule = $equipments[$i]->schedule - $days_difference->days;
                Log::info("Days to next schedule: " . $days_to_next_schedule . $equipments[$i]);

                if ($days_to_next_schedule == 5){
                    $schedule_maintenance = new ScheduleMaintanance();
                    $schedule_maintenance->equipment_id = $equipments[$i]->id;
                    $schedule_maintenance->date = $today->addDays(5);
                    $schedule_maintenance->save();

                    //Prepare data for email notification
                    $users = User::where('role_id',1)->get();
                    $data = [
                        'name'=>'',
                        'message'=>'You have a new scheduled maintenance in next 5 days',
                        'date'=>$schedule_maintenance->date,
                        'equipment'=>$equipments[$i]->name,
                        'department'=>$equipments[$i]->department->name,
                        'serial_number'=>$equipments[$i]->serial_no,
                    ];
                    for ($j = 0; $j < count($users); $j++){
                        $data['name'] = $users[$j]->name;
                        \Mail::to($users[$j]->email)->send(new ScheduledMaintenance($data));
                        $sendMessageController->send_order_message($data,$users[$j]->phone_number);
                    }

                }else if($days_to_next_schedule == 0){
                    //Prepare data for email notification
                    $users = User::where('role_id',1)->get();
                    $data = [
                        'name'=>'',
                        'message'=>'This is to remind you that today is maintenance day',
                        'date'=>$today->addDays($days_to_next_schedule),
                        'equipment'=>$equipments[$i]->name,
                        'department'=>$equipments[$i]->department->name,
                    ];
                    for ($j = 0; $j < count($users); $j++){
                        $data['name'] = $users[$j]->name;
                        \Mail::to($users[$j]->email)->send(new ScheduledMaintenance($data));
                        $sendMessageController->send_order_message($data,$users[$j]->phone_number);
                    }
                }
            }else{
                $today = Carbon::now();
                $schedule_maintenance = new ScheduleMaintanance();
                $schedule_maintenance->equipment_id = $equipments[$i]->id;
                $schedule_maintenance->date = $today->addDays($equipments[$i]->schedule);
                $schedule_maintenance->save();
            }
        }

        Log::info("Schedule Maintanance check Processed");
        return true;
    }

    public function get_scheduled_maintenance(){
        $scheduled_maintenance = ScheduleMaintanance::with('equipment.department')->where('is_done',0)->get();

        return response()->json($scheduled_maintenance);
    }

    public function save_report(Request $request, $id){
        $data = request()->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'status' => 'required'
        ]);

        $maintenance_report = new MaintananceReport();
        $maintenance_report->user_id = auth()->user()->id;
        $maintenance_report->schedule_maintanance_id = $id;
        $maintenance_report->equipment_id = $request->equipment;
        $maintenance_report->title = $request->title;
        $maintenance_report->description = $request->description;
        $maintenance_report->status = $request->status ? 1 : 0;
        $maintenance_report->save();

        $schedule_maintenance = ScheduleMaintanance::findOrFail($id);
        $schedule_maintenance->is_done = $maintenance_report->status == 1 ? 1 : 0;
        $schedule_maintenance->save();

        return response()->json([200,"Okay"]);
    }

    public function send_fault_report(Request $request,$qr_token){
        $data = request()->validate([
            'description' => 'required|string'
        ]);

        $equipment = Equipment::with('department')->where('qr_id',$qr_token)->get();

        $new_falt = new FaltReport();
        $new_falt->equipment_id = $equipment->id;
        $new_falt->description = $data->description;
        $new_falt->save();

        //Prepare data for email notification
        $users = User::where('role_id',1)->get();

        $sendMessageController = new SendMessageController();
        for ($i=0; $i < count($users); $i++) { 
            $data = array(
                "name"=>$users[$j]->name,
                "equipment_name"=>$equipment->name,
                "equipment_serial_no"=>$equipment->serial_no,
                "department"=>$equipment->department->name,
                "issue"=>$data->description
            );
            \Mail::to($users[$j]->email)->send(new FaultReport($data));
            $sendMessageController->send_fault_report("Fault Alert: " . $data['equipment_name'] . " Serial Number: " . $data['equipment_serial_no'] . " Department: " . $data['department'],$users[$i]->phone_number);
        }
    }

    public function get_falt_reports(){
        $reported_fault = FaltReport::with('equipment.department')->where('is_done',0)->get();
        return response()->json($reported_fault);
    }

    public function save_reported_fault(Request $request,$fault_reported){
        $data = request()->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'status' => 'required'
        ]);

        $maintenance_report = new FaltReportMaintenance();
        $maintenance_report->user_id = auth()->user()->id;
        $maintenance_report->falt_report_id = $fault_reported;
        $maintenance_report->title = $request->title;
        $maintenance_report->description = $request->description;
        $maintenance_report->status = $request->status ? 1 : 0;
        $maintenance_report->save();

        return response()->json([200,"Okay"]);
    }
}

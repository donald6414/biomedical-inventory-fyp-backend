<?php

namespace App\Http\Controllers;

use App\Mail\ScheduledMaintenance;
use App\Models\Equipment;
use App\Models\MaintananceReport;
use App\Models\ScheduleMaintanance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;

class ProcessController extends Controller
{
    public function check_schedule_maintenance(){
        $equipments = Equipment::with('latestScheduleMaintenance','department')->get();
        Log::info($equipments);

        for ($i = 0; $i < count($equipments); $i++){
            if ($equipments[$i]->latestScheduleMaintenance){
                $today = Carbon::now();
                $last_maintenance = $equipments[$i]->latestScheduleMaintenance->date;

                $days_difference = $today->diff(new \DateTime($last_maintenance));

                $days_to_next_schedule = $equipments[$i]->schedule - $days_difference->days;
                Log::info("Days to next schedule: " . $days_to_next_schedule);

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
                    ];
                    for ($j = 0; $j < count($users); $j++){
                        $data['name'] = $users[$j]->name;
                        \Mail::to($users[$j]->email)->send(new ScheduledMaintenance($data));
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
}

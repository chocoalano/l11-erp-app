<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GroupAttendance;
use App\Models\InAttendance;
use App\Models\OutAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function integratedFromMachine(Request $request){
        $in = [];
        $out = [];
        foreach ($request->all() as $k) {
            if (
                isset($k['id']) 
                && isset($k['nik'])
                && isset($k['time'])
                && isset($k['date'])
                && isset($k['dept'])
                && isset($k['shift'])
                && isset($k['status'])
                && isset($k['punch_state'])
                ) {
                if ($k['status'] === 'In') {
                    array_push($in,$k);
                } else {
                    array_push($out,$k);
                }
            }
        }
        if (count($in) > 0) {
            foreach ($in as $k) {
                $inCek = InAttendance::where(function($q)use($k){
                    $q->where('nik', $k['nik'])
                    ->whereDate('date', $k['date']);
                })->count();
                if ($inCek < 1) {
                    $i = new InAttendance();
                    $i->nik = $k['nik'];
                    $i->schedule_group_attendances_id = null;
                    $i->lat = (float)'-6.1749639';
                    $i->lng = (float)'106.598571,15';
                    $i->date = $k['date'];
                    $i->time = $k['time'];
                    $i->photo = null;
                    $i->status = null;
                    $i->save();
                    $i->attendance()->create([
                        'nik'=>$k['nik'],
                        'date'=>$k['date'],
                    ]);
                }
            }
        }
        if(count($out) > 0){
            foreach ($out as $k) {
                $i = OutAttendance::where(function($q)use($k){
                    $q
                    ->where('nik', $k['nik'])
                    ->where('date', $k['date']);
                })
                ->update([
                    'lat'=>(float)'-6.1749639',
                    'lng'=>(float)'106.598571,15',
                    'time'=>$k['time'],
                ]);
            }
        }
        return response()->json($request->all());  
    }
}
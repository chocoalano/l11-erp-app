<?php
namespace App\Http\Controllers\Api;

use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\AttendanceInOutRequest;
use App\Http\Requests\Attendance\AttendanceSyncRequest;
use App\Http\Requests\Attendance\AttendanceUpdateRequest;
use App\Interfaces\AttendanceInterface;
use App\Jobs\ProcessLargeData;
use App\Models\InAttendance;
use App\Models\OutAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    protected $proses;
    public function __construct(AttendanceInterface $proses)
    {
        $this->proses = $proses;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage=$request->input('perPage', 10);
        $page=$request->input('page', 1);
        $search=$request->input('search', '');
        $data = $this->proses->index($perPage, $page, $search);

        return ApiResponseClass::sendResponse($data,'',200);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(AttendanceInOutRequest $request)
    {
        DB::beginTransaction();
        try{
             $q = $this->proses->store($request->toArray());

             DB::commit();
             return ApiResponseClass::sendResponse($q,'Presence Successful',200);
        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $q = $this->proses->getById($id);

        return ApiResponseClass::sendResponse($q,'',200);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(AttendanceUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try{
             $q = $this->proses->update($request->toArray(),$id);

             DB::commit();
             return ApiResponseClass::sendResponse($q, 'Update Successful',201);
        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
         $this->proses->delete($id);

        return ApiResponseClass::sendResponse('Delete Successful','',204);
    }
    public function integratedFromMachine(Request $request){
        DB::beginTransaction();
        try{
            $data = $request->all();
            $q = ProcessLargeData::dispatch($data['data']);
            DB::commit();
            return ApiResponseClass::sendResponse($q,'Presence Successful',200);
        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
        // $q = $this->proses->sync($request->toArray());
        // return $q;
        // $q = $this->proses->sync($request->toArray());
        // return ApiResponseClass::sendResponse($q,'Presence Successful',200);
        // $in = [];
        // $out = [];
        // foreach ($request->all() as $k) {
        //     if (
        //         isset($k['id']) 
        //         && isset($k['nik'])
        //         && isset($k['time'])
        //         && isset($k['date'])
        //         && isset($k['dept'])
        //         && isset($k['shift'])
        //         && isset($k['status'])
        //         && isset($k['punch_state'])
        //         ) {
        //         if ($k['status'] === 'In') {
        //             array_push($in,$k);
        //         } else {
        //             array_push($out,$k);
        //         }
        //     }
        // }
        // if (count($in) > 0) {
        //     foreach ($in as $k) {
        //         $inCek = InAttendance::where(function($q)use($k){
        //             $q->where('nik', $k['nik'])
        //             ->whereDate('date', $k['date']);
        //         })->count();
        //         if ($inCek < 1) {
        //             $i = new InAttendance();
        //             $i->nik = $k['nik'];
        //             $i->schedule_group_attendances_id = null;
        //             $i->lat = (float)'-6.1749639';
        //             $i->lng = (float)'106.598571,15';
        //             $i->date = $k['date'];
        //             $i->time = $k['time'];
        //             $i->photo = null;
        //             $i->status = null;
        //             $i->save();
        //             $i->attendance()->create([
        //                 'nik'=>$k['nik'],
        //                 'date'=>$k['date'],
        //             ]);
        //         }
        //     }
        // }
        // if(count($out) > 0){
        //     foreach ($out as $k) {
        //         $i = OutAttendance::where(function($q)use($k){
        //             $q
        //             ->where('nik', $k['nik'])
        //             ->where('date', $k['date']);
        //         })
        //         ->update([
        //             'lat'=>(float)'-6.1749639',
        //             'lng'=>(float)'106.598571,15',
        //             'time'=>$k['time'],
        //         ]);
        //     }
        // }
        // return response()->json($request->all());  
    }
}
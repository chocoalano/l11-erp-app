<?php
namespace App\Http\Controllers\Api;

use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\AttendanceInOutRequest;
use App\Http\Requests\Attendance\AttendanceUpdateRequest;
use App\Interfaces\AttendanceInterface;
use App\Jobs\ProcessLargeData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    }
}
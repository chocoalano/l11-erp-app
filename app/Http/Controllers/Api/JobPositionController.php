<?php

namespace App\Http\Controllers\Api;

use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Interfaces\Hris\JobPositionInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobPositionController extends Controller
{
    
    protected $proses;
    public function __construct(JobPositionInterface $proses)
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
    public function store(Request $request)
    {
        DB::beginTransaction();
        try{
             $q = $this->proses->store($request->toArray());
             DB::commit();
             return ApiResponseClass::sendResponse($q,'Presence Successful',200);
        }catch(\Exception $ex){
            dd($ex);
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

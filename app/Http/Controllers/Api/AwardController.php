<?php

namespace App\Http\Controllers\Api;

use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Interfaces\Marketing\AwardInterface;
use Illuminate\Http\Request;

class AwardController extends Controller
{
    protected $proses;
    public function __construct(AwardInterface $proses)
    {
        $this->proses = $proses;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
            $q = $this->proses->getById($id);
            return ApiResponseClass::sendResponse($q,'',200);
    }
    public function showAllActive(Request $request)
    {
            $input = $request->only(['perPage','page','search']);
            $perPage=$input['perPage'];
            $page=$input['page'];
            $search=$input['search'];
            $q = $this->proses->getAllActiveStatus($perPage,$page,$search);
            $seo = $this->proses->getSeoPage();
            return ApiResponseClass::sendResponse(['seo'=>$seo, 'list'=>$q],'',200);
    }
    public function showFromSlug($slug)
    {
        $q = $this->proses->getFromSlugData($slug);
        return ApiResponseClass::sendResponse($q,'',200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}

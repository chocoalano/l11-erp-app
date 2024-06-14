<?php

namespace App\Http\Controllers\Api;

use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ProfileUpdateRequest;
use App\Interfaces\AuthenticationInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    protected $proses;
    public function __construct(AuthenticationInterface $proses)
    {
        $this->proses = $proses;
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_or_nik' => 'required|string',
            'password' => 'required|string',
        ]);
        if($validator->fails()){
            return ApiResponseClass::throw('Validation Error.', $validator->errors());       
        }
        $auth=$this->proses->login($request->input('email_or_nik'), $request->input('password'));
        if (!is_null($auth)) {
            $token = $auth->createToken('MyApp')->plainTextToken;
            $response['token'] =  $token;
            $response['name'] =  $auth->name;
            return ApiResponseClass::sendResponse($response,'',200);
        }
        return ApiResponseClass::sendResponse('These credentials do not match our records.','',401);
    }
    public function profile()
    {
        $auth=$this->proses->profile();
        $response['user']=$auth['user'];
        $response['authorization']=$auth['authorization'];
        return ApiResponseClass::sendResponse($response,'',200);
    }
    public function profile_update(ProfileUpdateRequest $request)
    {
        DB::beginTransaction();
        try{
             $q = $this->proses->updateProfile($request->toArray());

             DB::commit();
             return ApiResponseClass::sendResponse($q, 'Update Successful',200);

        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }
    public function logout(Request $request)
    {
        $tokenId = $request->input('token_id');

        if ($this->proses->logout($tokenId)) {
            return response()->json(['message' => 'Token revoked successfully.'], 200);
        }

        return response()->json(['message' => 'Token not found or already revoked.'], 404);
    }
}

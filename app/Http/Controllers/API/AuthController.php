<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
    public function index(Request $request){
        $validator = Validator::make($request->all(), [
           
            'email' => 'required|email',
            'password' => 'required',
           
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(),422);       
        }
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $data['token'] =  $user->createToken('my-token')->plainTextToken;
            $data['name'] =  $user->name;
   
            return $this->sendResponse($data, 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorize.', ['error'=>'Wrong email or password'],401);
        } 
    }
}

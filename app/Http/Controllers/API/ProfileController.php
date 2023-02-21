<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends BaseController
{
    public function show(){
        $user = User::find(auth()->user()->id);
        if($user->employee){
            return $this->sendResponse($user->employee, 'Success get data'); 
        }
        return $this->sendResponse($user, 'Success get data'); 
    }
    public function update(Request $request){
        $row = User::find(auth()->user()->id);
        if (!$row)
            return $this->sendError('Warning.', ['error' => ['Users not found']], 422);

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $row->id . ',id',

        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }


        if (trim($request->password)) {
            $request['password'] = bcrypt($request['password']);
            $data = $request->only(['email','name','password']);
        }else{
            $data = $request->only(['email','name']);
        }
        $row->update($data);


        return $this->sendResponse($row->id, 'Profile successfully updated.');
    }

    public function biodata(Request $request){
        $employee = Employee::whereRelation('users', 'id', auth()->user()->id)->first();
        if (!$employee) {
            return $this->sendError('Warning.', ['error' => ['Employee not found']], 404);
        }
        $validator = Validator::make($request->all(), [
            'nip' => 'required|unique:employee,id,' . $employee->id . '|max:20',
            'fullname' => 'required|max:255',
            'gender' => 'required|in:male,female',
            'phone' => 'required|max:20',
            'pob' => 'required|max:100',
            'dob' => 'required|date',
            'address' => 'required',
           
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }
        $employee->update($request->all());
        return $this->sendResponse($employee->users_id, 'Success updated biodata');
    
    }
}

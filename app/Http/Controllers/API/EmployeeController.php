<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EmployeeController extends BaseController
{
    public function read(Request $request)
    {
        $data = Employee::where($request->all())->orderBy('updated_at')->simplePaginate(10);
        return $this->sendResponse($data,'Success get data');
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'users_id' =>'required|exists:users,id|unique:employee',
            'nip' => 'required|unique:employee|max:20',
            'fullname'=>'required|max:255',
            'gender'=>'required|in:male,female',
            'phone'=>'required|max:20',
            'pob'=>'required|max:100',
            'dob'=>'required|date',
            'address'=>'required',
            'balance'=>'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(),422); 
        }
        if(!User::where(['id'=>$request->users_id,'role'=>'employee'])->first())
            return $this->sendError('Warning.', ['users_id'=>['Users not in role employee']],422); 
            
        $employee = Employee::create($request->all());
        return $this->sendResponse($employee->id,'Success created employee');

    }
}

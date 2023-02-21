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
    public function show(Request $request)
    {
        $search = $request->except(['page']);
        $data = Employee::where($search)->orderBy('updated_at')->simplePaginate(10);
        return $this->sendResponse($data, 'Success get data');
    }
    public function dropdown(Request $request)
    {
        $data = Employee::where($request->all())->orderBy('fullname')->get()->map(function ($item) {
            return $item->toDropdown();
        });
        return $this->sendResponse($data, 'Success get data');
    }
    public function find($email)
    {
        $data = Employee::whereRelation('users', 'email', $email)->first();
        if (!$data)
            return $this->sendError('Warning.', ['error' => ['Employee not found']], 422);
        return $this->sendResponse($data, 'Success get employee');
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'users_id' => 'required|exists:users,id|unique:employee',
            'nip' => 'required|unique:employee|max:20',
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
        if (!User::where(['id' => $request->users_id, 'role' => 'employee'])->first())
            return $this->sendError('Warning.', ['users_id' => ['Users not in role employee']], 422);
        $data = $request->only(['users_id','nip','fullname','gender','phone','pob','dob','address']);
        $employee = Employee::create($data);
        return $this->sendResponse($employee->id, 'Success created employee');
    }
    public function update(Request $request, $email)
    {
        $employee = Employee::whereRelation('users', 'email', $email)->first();
        if (!$employee) {
            return $this->sendError('Warning.', ['error' => ['Employee not found']], 404);
        }
        $validator = Validator::make($request->all(), [
            'users_id' => 'required|exists:users,id|unique:employee,id,' . $employee->id . '',
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
        return $this->sendResponse($employee->id, 'Success updated employee');
    }
    public function destroy($email)
    {
        $data = Employee::whereRelation('users', 'email', $email)->first();
        if (!$data)
            return $this->sendError('Warning.', ['error' => ['Employee not found']], 422);
        $data->delete();
        return $this->sendResponse('success', 'Success get employee');
    }
}

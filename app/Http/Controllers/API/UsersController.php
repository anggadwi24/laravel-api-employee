<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsersController extends BaseController
{
    public function show(Request $request)
    {
        $data = User::where($request->all())->orderBy('updated_at')->simplePaginate(10);
        return $this->sendResponse($data, 'Success get data');
    }
    public function find($email)
    {
        $data = User::where('email', $email)->first();
        if (!$data)
            return $this->sendError('Warning.', ['error' => ['Users not found']], 422);
        return $this->sendResponse($data, 'Success get data');
    }
    public function dropdown(Request $request)
    {
        $data = User::where($request->all())->orderBy('name')->get()->map(function ($item) {
            return $item->toDropdown();
        });
        return $this->sendResponse($data, 'Success get data');
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'role' => 'required|in:admin,employee',

        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $input = $request->all();

        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);


        return $this->sendResponse($user->id, 'User created successfully.');
    }
    public function update(Request $request, $email)
    {
        $row = User::where('email', $email)->first();
        if (!$row)
            return $this->sendError('Warning.', ['error' => ['Users not found']], 422);

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $row->id . ',id',
            'role' => 'required|in:admin,employee',

        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }


        if ($request->passowrd) {
            $request['password'] = bcrypt($request['password']);
        }
        $row->update($request->all());


        return $this->sendResponse($row->id, 'User updated successfully.');
    }
    public function destroy($email)
    {
        $data = User::where('email', $email)->first();
        if (!$data)
            return $this->sendError('Warning.', ['error' => ['Users not found']], 422);
        if ($data->employee) {
            $data->employee->delete();
        }
        $data->delete();
        return $this->sendResponse('success', 'Success delete data');
    }
}

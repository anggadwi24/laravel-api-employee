<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\LeaveBalance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BalanceEmployeeController extends BaseController
{
    public function submission(Request $request){
        $email = auth()->user()->email;
        $row = Employee::whereRelation('users', 'email', $email)->first();
        if (!$row)
            return $this->sendError('Warning.', ['error' => ['Employee not found']], 422);
        
        $validator = Validator::make($request->all(), [
            'balance' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }
        if($row->balance < $request->balance){
            return $this->sendError('Warning',['error'=>'Insufficient employee balance'], 422);
        }
        $balance = $row->balance-$request->balance;
        $data['employee_id'] = $row->id;
        $data['balance'] = $request->balance;
        $data['dates'] = Carbon::now();
        $data['flow'] = 'out';
        
       
        LeaveBalance::create($data);
        return $this->sendResponse($row, 'Submission has been successfully submitted');
    }
    public function history(Request $request){
        $email = auth()->user()->email;
        $row = Employee::whereRelation('users', 'email', $email)->first();
        if (!$row)
            return $this->sendError('Warning.', ['error' => ['Employee not found']], 422);
        $data = LeaveBalance::where($request->all())->orderBy('id','desc')->get();
        return $this->sendResponse($data, 'Success get leave balance');
    }
}

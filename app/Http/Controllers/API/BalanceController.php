<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Employee;
use App\Models\LeaveBalance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BalanceController extends BaseController
{
    public function show(Request $request)
    {
        $data = LeaveBalance::select('employee.fullname', 'leave_balance.dates as date', 'leave_balance.flow', 'leave_balance.balance', 'leave_balance.balance_now', 'leave_balance.isApprove as approve')
            ->leftJoin('employee', 'leave_balance.employee_id', '=', 'employee.id')
            ->where($request->all())
            ->orderBy('leave_balance.id', 'desc')
            ->get();
        return $this->sendResponse($data, 'Success get employee');
    }

    public function find($id)
    {
        $data = LeaveBalance::find($id);
        if (!$data) {
            return $this->sendError('Warning.', ['error' => ['Leave Balance not found']], 422);
        }
        return $this->sendResponse($data, 'Success get leave balance');
    }

    public function findByEmail($email)
    {
        $data = LeaveBalance::whereRelation('employee.users', 'email', $email);
        if (!$data) {
            return $this->sendError('Warning.', ['error' => ['Leave Balance not found']], 422);
        }
        return $this->sendResponse($data->get(), 'Success get leave balance');
    }

    public function in(Request $request, $email)
    {
        $row = Employee::whereRelation('users', 'email', $email)->first();
        if (!$row)
            return $this->sendError('Warning.', ['error' => ['Employee not found']], 422);

        $validator = Validator::make($request->all(), [
            'balance' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }
        $balance = $row->balance + $request->balance;
        $data['employee_id'] = $row->id;
        $data['balance'] = $balance;
        $data['dates'] = Carbon::now();
        $data['flow'] = 'in';
        $data['balance_now'] = $balance;
        $data['isApprove'] = 'y';
        $row->update(['balance' => $balance]);
        LeaveBalance::create($data);
        return $this->sendResponse($row, 'Success update employee leave balance');
    }

    public function out(Request $request, $email)
    {
        $row = Employee::whereRelation('users', 'email', $email)->first();
        if (!$row)
            return $this->sendError('Warning.', ['error' => ['Employee not found']], 422);

        $validator = Validator::make($request->all(), [
            'balance' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }
        if ($row->balance < $request->balance) {
            return $this->sendError('Warning', ['error' => 'Insufficient employee balance'], 422);
        }
        $balance = $row->balance - $request->balance;
        $data['employee_id'] = $row->id;
        $data['balance'] = $request->balance;
        $data['dates'] = Carbon::now();
        $data['flow'] = 'out';
        $data['balance_now'] = $balance;
        $data['isApprove'] = 'y';
        $row->update(['balance' => $balance]);
        LeaveBalance::create($data);
        return $this->sendResponse($row, 'Success retrieve employee leave balance');
    }
    
    public function submission($id, $status)
    {
        if ($status == 'approve'  or $status == 'disapprove') {
            $row = LeaveBalance::find($id);
            if (!$row) {
                return $this->sendError('Warning', ['error' => 'Leave balance not found'], 422);
            }

            if ($row->isApprove) {
                return $this->sendError('Warning', ['error' => 'Leave balance not in status waiting'], 422);
            }

            $status = $status  == 'disapprove' ? 'n' : 'y';
            $balance = $row->employee->balance - $row->balance;
            $row->update(['isApprove' => $status,'balance_now'=>$balance]);
            if ($status == 'y') {
                $row->employee->update(['balance' => $balance]);
            }
            return $this->sendResponse($row, 'Success submission employee leave balance');
        } else {
            return $this->sendError('Warning', ['error' => 'Invalid status selection'], 422);
        }
    }
}

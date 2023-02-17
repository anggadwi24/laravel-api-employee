<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveBalance extends Model
{
    use HasFactory,SoftDeletes,Blameable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'leave_balance';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['employee_id','balance','flow','balance_now'];

    public function employee(){
        return $this->belongsTo(Employee::class);
    }


}

<?php

namespace App\Models;

use App\Blameable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class LeaveBalance extends Model
{
    use HasFactory,Blameable;

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
    protected $fillable = ['employee_id','balance','flow','balance_now','dates','isApprove'];
    
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['date','approve'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['created_at','created_by','updated_by','employee'];
    public function employee(){
        return $this->belongsTo(Employee::class);
    }
    public function getDateAttribute(){
        return Carbon::parse($this->dates)->format('d F Y H:i');
    }
    public function getApproveAttribute(){
        $value = $this->isApprove;
        if($value == null){
            return 'Waiting';
        }else{
            
            if($value == 'y'){
                return 'Approve';
            }else if($value == 'n'){
                return 'Disapprove';
            }
        }
    }


}

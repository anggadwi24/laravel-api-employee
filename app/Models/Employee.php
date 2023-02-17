<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory,SoftDeletes,Blameable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $fillable = ['users_id','nip','fullname','gender','phone','pob','dob','address','balance'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['created_at','updated_at','deleted_at','created_by','updated_by'];
    public function leaveBalance(){
        return $this->hasMany(LeaveBalance::class);
    }
    public function users(){
        return $this->belongsTo(User::class,'users_id');
    }
}

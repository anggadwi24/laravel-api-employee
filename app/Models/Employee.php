<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Employee extends Model
{
    use HasFactory,Blameable;

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
    
    protected $hidden = ['created_by','updated_by','users','id','users_id'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['email','name'];

    public function leaveBalance(){
        return $this->hasMany(LeaveBalance::class);
    }
    public function users(){
        return $this->belongsTo(User::class,'users_id');
    }
    public function getEmailAttribute(){
        return $this->users->email;
    }
    public function getNameAttribute(){
        return $this->users->name;
    }
}

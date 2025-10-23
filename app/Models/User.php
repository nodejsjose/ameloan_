<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = [
        'name', 'email', 'password', 'user_type', 'status', 'profile_picture',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
	
	public function getCreatedAtAttribute($value)
    {
		$date_format = get_date_format();
		$time_format = get_time_format();
        return \Carbon\Carbon::parse($value)->format("$date_format $time_format");
    }
	
	public function role(){
		return $this->belongsTo('App\Models\Role','role_id')->withDefault();
	}

    public function member(){
		return $this->hasOne('App\Models\Member','user_id')->withDefault();
	}

    // Single branch relation (kept for backward compatibility)
    public function branch(){
        return $this->belongsTo('App\Models\Branch','branch_id')->withDefault();
    }

    // Many-to-many branches assigned to the user
    public function branches(){
        return $this->belongsToMany('App\Models\Branch', 'branch_user', 'user_id', 'branch_id')->withTimestamps();
    }

    /**
     * Return array of assigned branch IDs (including branch_id field if set)
     */
    public function assignedBranchIds(){
        $ids = $this->branches()->pluck('branches.id')->toArray();
        if($this->branch_id && !in_array($this->branch_id, $ids)){
            $ids[] = $this->branch_id;
        }
        return $ids;
    }
}

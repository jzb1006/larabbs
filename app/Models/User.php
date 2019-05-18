<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable,HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','introduction','avatar',
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
     * 这里使用了laravel的修改器功能，当数据写入数据库前执行的方法
    */
    public function setPasswordAttribute($value){
        //如果值得长度等于60就不用使用修改器修改密码
        if(strlen($value) != 60){
            $value = bcrypt($value);
        }

        $this->attributes['password'] = $value;

    }

    /**
     * 和上面的一样
    */
    public function setAvatarAttribute($path){
        if(starts_with($path,'http')){
            $path = config('app.url')."/uploads/images/avatar/$path";
        }
        $this->attributes['avatar'] = $path;
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function topics(){
        return $this->hasMany(Topic::class);
    }

    public function isAuthorOf($model){
        return $this->id == $model->user_id;
    }

    public function replies(){
        return $this->hasMany(Reply::class);
    }
}

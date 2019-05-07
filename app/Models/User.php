<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
class User extends Authenticatable implements MustVerifyEmailContract
{
    use MustVerifyEmail;
    use Notifiable{
        notify as protected laravelNotify;
    }

    public function notify($instance){
        //如果要通知的人是当前用户没就不必通知了
        if($this->id==Auth::id()){
            return;
        }

        //只有数据库类型通知才提醒，直接发送Email 或者其他的Pass
        if(method_exists($instance,'toDatabase')){
            $this->increment('notification_count');
        }

        $this->laravelNotify($instance);
    }

    public function markAsRead(){
        $this->notification_count = 0;
        $this->save();
        $this->unreadNotifications->markAsRead();
    }
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

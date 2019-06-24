<?php

namespace App\Models;

use App\Models\Traits\ActiveUserHelper;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
class User extends Authenticatable implements JWTSubject
{
    use Notifiable,HasRoles;
    use ActiveUserHelper;
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
        'name', 'email', 'phone','password','introduction','avatar',
    'weixin_openid', 'weixin_unionid','registration_id'
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
        dd('sdsdd');
        return $this->id == $model->user_id;
    }

    public function replies(){
        return $this->hasMany(Reply::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
}

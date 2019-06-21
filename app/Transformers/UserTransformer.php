<?php
/**
 * Created by PhpStorm.
 * User: JIANG
 * Date: 2019/6/4
 * Time: 10:13
 */
namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract{
    protected $availableIncludes = ['roles'];
    public  function transform(User $user){
        return [
          'id' => $user->id,
          'name' => $user->name,
          'email' =>$user->email,
          'avatar'=>$user->avatar,
          'introduction'=>$user->introduction,
          'bound_phone'=> $user->phone?true:false,
          'bound_wechat'=>($user->weixin_unionid||$user->weixin_openid)?true:false,
//          'last_active_at'=>$user->last_active_at->toDateTimeString(),
          'created_at'=>$user->created_at->toDateTimeString(),
          'updated_at'=>$user->updated_at->toDateTimeString()
        ];
    }

    public function includeRoles(User $user){
        return $this->collection($user->roles,new RoleTransformer());
    }
}



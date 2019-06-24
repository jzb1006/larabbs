<?php
/**
 * Created by PhpStorm.
 * User: JIANG
 * Date: 2019/6/24
 * Time: 13:51
 */

namespace Tests\Traits;


use App\Models\User;

trait ActingJWTUser
{
  public function JWTActingAs(User $user){
      $token = \Auth::guard('api')->fromUser($user);
      $this->withHeaders(['Authorization'=>'Bearer '.$token]);
      return $this;
  }
}

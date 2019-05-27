<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UsersController extends Controller
{
    //
    public function store(UserRequest $request){
        $verifyData  = Cache::get($request->verification_key);
        if(!$verifyData){
            return $this->response->error('验证码失效',422);
        }
        if(!hash_equals($verifyData['code'],$request->verification_code)){
            return $this->response->errorUnauthorized('验证码有误');
        }

        $user = User::create([
            'name'=>$request->name,
            'phone'=>$verifyData['phone'],
            'password'=>bcrypt($request->password)
        ]);

        //清除缓存
        Cache::forget($request->verification_key);
        return $this->response->created();
    }
}

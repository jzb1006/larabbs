<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UserRequest;
use App\Models\Image;
use App\Models\User;
use App\Transformers\UserTransformer;
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
        Cache::forget($request->verification_key);
        return $this->response
            ->item($user,new UserTransformer())
            ->setMeta([
                'access_token'=>\Auth::guard('api')->fromUser($user),
                'token_type'=>'Bearer',
                'expire_in'=>\Auth::guard('api')->factory()->getTTL()*60
            ])
            ->setStatusCode(201);

    }

    public function me(){
        return $this->response->item($this->user(),new UserTransformer());
    }

    public function update(UserRequest $request){
        $user = $this->user();
        $attributes = $request->only(['name','email','introduction']);
        if($image_id = $request->avatar_image_id){
            $imageInfo = Image::find($image_id);
            $attributes['avatar'] = $imageInfo->path;
        }
        $user->update($attributes);
        return $this->response->item($user,new UserTransformer());
    }
}

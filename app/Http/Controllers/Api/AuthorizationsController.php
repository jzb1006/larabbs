<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AuthorizationRequest;
use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class AuthorizationsController extends Controller
{
    //
    public function socialStore($type,SocialAuthorizationRequest $request){
        //通过保存access_token,openid在客户端,登陆时传递对应的值登陆
//        $accessToken = 'ACCESS_TOKEN';
////        $openID = 'OPEN_ID';
////        $driver = Socialite::driver('weixin');
////        $driver->setOpenId($openID);
////        $oauthUser = $driver->userFromToken($accessToken);
        if(!in_array($type,['weixin'])){
            return $this->response->errorBadRequest();
        }
        $driver = Socialite::driver($type);
        try{
            if($code = $request->code){
                $response = $driver->getAccessTokenResponse($code);
                $token = array_get($response,'access_token');
            }else{
                $token = $request->access_token;
                if($type=='weixin'){
                    $driver->setOpenId($request->openid);
                }
            }
            $oauthUser = $driver->userFromToken($token);
        }catch (\Exception $e){

            return $this->response->errorUnauthorized('参数有误，未能获取到用户信息');
        }

        switch ($type){
            case 'weixin':
                $unionid = $oauthUser->offsetExists('unionid')?$oauthUser->offsetGet('unionid'):null;
                if($unionid){
                    //优先使用 unionid 查找，因为同一个用户可能创建多个不同的应用,而这种情况下unionid是唯一的
                    $user = User::where('weixin_unionid',$unionid)->first();
                }else{
                    $user = User::where('weixin_openid',$oauthUser->getId())->first();
                }

                //没有用户就创建
               if(!$user){
                   $user = User::create([
                       'name'=>$oauthUser->getNickname(),
                       'avatar'=>$oauthUser->getAvatar(),
                       'weixin_openid'=>$oauthUser->getId(),
                       'weixin_unionid'=>$unionid,
                   ]);
               }
               break;
        }
        $token = Auth::guard('api')->fromUser($user);
        return $this->respondWithToken($token);
    }

    public function store(AuthorizationRequest $request){
        $username = $request->username;
        $password = $request->password;
        if(filter_var($username,FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = $username;
        }else {
            $credentials['name'] = $username;
        }
        $credentials['password'] = $password;
        if(!$token = Auth::guard('api')->attempt($credentials)){
            return $this->response->errorUnauthorized(trans('auth.failed'));
        }

        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        return $this->response->array([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL()*60
        ]);
    }

    public function update(){
        $token = Auth::guard('api')->refresh();
        return $this->respondWithToken($token);
    }

    public function destroy(){
        Auth::guard('api')->logout();
        return $this->response->noContent();
    }
}

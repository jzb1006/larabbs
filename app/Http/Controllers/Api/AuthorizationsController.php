<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        return $this->response->array(['token' => $user->id]);
//        $token = Auth::guard('api')->fromUser($user);
//        return $this->respondWithToken($token)->setStatusCode(201);

    }
}

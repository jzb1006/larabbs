<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\VerificationCodesRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Overtrue\EasySms\EasySms;
class VerificationCodesController extends Controller
{
    //
    public function store(VerificationCodesRequest $request,EasySms $easySms){
        $captchaData = Cache::get($request->captcha_key);
        if (!$captchaData) {
            return $this->response->error('图片验证码已失效',422);
        }
        if(!hash_equals($captchaData['code'],$request->captcha_code)){
            //验证码错误就清除缓存
            Cache::forget($request->captcha_key);
            return $this->response->errorUnauthorized('验证码错误');
        };

        $phone = $captchaData['phone'];
        if(!app()->environment('production')) {
            $code = '1234';
        }else {
            $code = str_pad(mt_rand(1,9999),4,0,STR_PAD_LEFT);
            try{
                $result = $easySms->send($phone,[
                    'content'  =>  "您的验证码是{$code}"
                ]);
            }catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception) {
                $message = $exception->getException('yunpian')->getMessage();
                return $this->response->errorInternal($message ?? '短信发送异常');
            }
        }
        $verification_key = 'verificationCode_'.str_random(15);
        $expiredAt = now()->addMinute(10);
//        Cache::forget($key);
        Cache::put($verification_key,['phone'=>$phone,'code' => $code],$expiredAt);

        //清除图片缓存
        Cache::forget($request->captcha_key);
        return $this->response->array([
            'key' => $verification_key,
            'expired_at' => $expiredAt->toDateTimeString()
        ])->setStatusCode(201);
    }

}

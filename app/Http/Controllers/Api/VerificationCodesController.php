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
        $phone = $request->phone;
//        $key = $request->captcha_key;
//        $captcha_data = Cache::get($key);
//        if(!$captcha_data) {
//            return $this->response->error('验证码已过期',422);
//        }
//        if(!hash_equals($request->captcha_code,$captcha_data['code'])) {
//            //清除缓存
//            Cache::forget($key);
//            return $this->response->errorUnauthorized('验证码有误');
//        }
//        $phone = $captcha_data['phone'];
        if(!app()->environment('production')) {
            $code = '6686';
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
        return $this->response->array([
            'key' => $verification_key,
            'expired_at' => $expiredAt->toDateTimeString()
        ])->setStatusCode(201);
    }

}

<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
$api = app('Dingo\Api\Routing\Router');
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
$api->version('v1',[
    'namespace'=>'App\Http\Controllers\Api'
],function ($api){
    $api->group([
        'middleware' => ['api.throttle','serializer:array'],
        'limit'=>config('api.rate_limit.sign.limit'),
        'expires'=>config('api.rate_limit.sign.expires')
    ],function ($api){
        //短信验证
        $api->post('verificationCodes','VerificationCodesController@store')->name('api.verificationCodes.store');
        //用户注册
        $api->post('users','UsersController@store')->name('api.users.store');
        //验证码
        $api->post('captcha','CaptchasController@store')->name('api.captchas.store');
        //第三方登陆
        $api->post('socials/{social_type}/authorizations','AuthorizationsController@socialStore')->name('api.socials.authorizations.socialStore');
       //登陆获取Token
        $api->post('authorizations','AuthorizationsController@store')->name('api.authorizations.store');
        //刷新和销毁Token
        $api->put('authorizations/current','AuthorizationsController@update')->name('api.authorizations.update');
        $api->delete('authorizations/current','AuthorizationsController@destroy')->name('api.authorizations.delete');

        //需要Token验证的接口
        $api->group(['middleware'=>['api.auth']],function ($api){
            //当前登陆的用户信息
            $api->get('user','UsersController@me')->name('api.user.show');
        });

    });

});

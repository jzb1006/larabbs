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
        $api->group(['middleware'=>['api.auth','bindings']],function ($api){
            //当前登陆的用户信息
            $api->get('user','UsersController@me')->name('api.user.show');

            //用户编辑资料
            $api->patch('user','UsersController@update')->name('api.user.show');
            //图片资源
            $api->post('images','ImagesController@store')->name('api.images.story');
           //添加话题
            $api->post('topics','TopicsController@store')->name('api.topics.store');
            //修改话题
            $api->patch('topics/{topic}','TopicsController@update')
            ->name('api.topics.update');
            //删除话题
            $api->delete('topics/{topic}','TopicsController@destroy')
            ->name('api.topic.destroy');
        });

    });

    //游客可以访问的接口
    $api->get('categories','CategoriesController@index')
    ->name('api.categories.index');

});

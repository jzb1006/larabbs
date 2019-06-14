<?php

namespace App\Providers;

use App\Models\Reply;
use App\Observers\ReplyObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //这里可以设置扩展包的加载
        if(app()->isLocal()){
            $this->app->register(\VIACreative\SudoSu\ServiceProvider::class);
        }

        //手动修改模型绑定找不到数据的报错提示
        \API::error(function(\Illuminate\Database\Eloquent\ModelNotFoundException $exception){
            abort(404);
        });

        //权限不通过时，将500状态码自定义为403
        \API::error(function(\Illuminate\Auth\Access\AuthorizationException $exception){
            abort(403);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        \App\Models\Topic::observe(\App\Observers\TopicObserver::class);
//        Reply::observe(ReplyObserver::class);

    }
}

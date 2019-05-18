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
        Reply::observe(ReplyObserver::class);

    }
}

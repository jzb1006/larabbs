<?php

namespace App\Listeners;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PushNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(DatabaseNotification $notification,$event)
    {
        //本地环境默认不推送
        if(app()->environment('local')){
            return ;
        }
        $user = $notification->notifiablel;
        //没有registrations_id 的不推送
        if(!$user->registration_id){
            return ;
        }

        //推送消息
        $this->client->push()
            ->setPlatform('all')
            ->addRegistrationId($user->registration_id)
            ->setNotificationAlert(strip_tags($notification->notifiable))
            ->send();

    }
}

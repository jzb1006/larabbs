<?php
/**
 * Created by PhpStorm.
 * User: JIANG
 * Date: 2019/6/15
 * Time: 13:35
 */

namespace App\Transformers;


use Illuminate\Notifications\DatabaseNotification;
use League\Fractal\TransformerAbstract;

class NotificationTransformer extends TransformerAbstract
{
   public function transform(DatabaseNotification $notification){
       return [
           'id'=>$notification->id,
           'type'=>$notification->type,
           'data'=>$notification->data,
           'read_at'=>$notification->read_at?$notification->read_ad->toDateTimeString():null,
           'created_at'=>$notification->created_at->toDateTimeString(),
           'updated_at'=>$notification->updated_at->toDateTimeString()
       ];
   }
}

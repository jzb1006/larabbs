<?php

namespace App\Observers;

use App\Models\Reply;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class ReplyObserver
{


    public function creating(Reply $reply){

        $reply->content = clean($reply->countent,'user_topic_body');
    }

    public function created(Reply $reply)
    {
        $reply->topic->reply_count = $reply->topic->replies->count();
        $reply->topic->save();
    }
}
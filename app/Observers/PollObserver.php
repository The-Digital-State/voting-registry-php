<?php

namespace App\Observers;

use App\Models\Poll;

trait PollObserver
{
    protected static function boot()
    {
        parent::boot();

        static::saving(function (Poll $poll) {
            $poll->started_at = !empty($poll->started_at) ? $poll->started_at : null;
            $poll->started_at = !empty($poll->ended_at) ? $poll->ended_at : null;
            $poll->emails_list_id = !empty($poll->emails_list_id) ? $poll->emails_list_id : null;
        });
    }
}

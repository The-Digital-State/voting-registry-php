<?php

namespace App\Observers;

use App\Models\Poll;

class PollObserver
{
    /**
     * Handle the Poll "saving" event.
     *
     * @param \App\Models\Poll $poll
     * @return void
     * @throws \Exception
     */
    public function saving(Poll $poll)
    {
        $poll->start = !empty($poll->start) ? $poll->start : null;
        $poll->end = !empty($poll->end) ? $poll->end : null;
        $poll->emails_list_id = !empty($poll->emails_list_id) ? $poll->emails_list_id : null;
    }
}

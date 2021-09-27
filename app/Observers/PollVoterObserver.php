<?php

namespace App\Observers;

use App\Models\PollVoter;

class PollVoterObserver
{
    /**
     * Handle the PollVoter "saving" event.
     *
     * @param \App\Models\PollResult $pollResult
     * @return void
     * @throws \Exception
     */
    public function saving(PollVoter $pollVoter)
    {
        if (!$pollVoter->voted_at) {
            $pollVoter->voted_at = now();
        }
    }
}

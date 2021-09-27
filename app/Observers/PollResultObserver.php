<?php

namespace App\Observers;

use App\Models\PollResult;

class PollResultObserver
{
    /**
     * Handle the PollResult "saving" event.
     *
     * @param \App\Models\PollResult $pollResult
     * @return void
     * @throws \Exception
     */
    public function saving(PollResult $pollResult)
    {
        $pollResult->token = bin2hex(random_bytes(16));
    }
}

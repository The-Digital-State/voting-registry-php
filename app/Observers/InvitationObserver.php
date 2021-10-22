<?php

namespace App\Observers;

use App\Models\Invitation;
use Illuminate\Support\Str;

class InvitationObserver
{
    /**
     * Handle the Invitation "saving" event.
     *
     * @param \App\Models\Invitation $invitation
     * @return void
     */
    public function saving(Invitation $invitation)
    {
        if (!$invitation->token) {
            $invitation->token = (string)Str::uuid();
        }
    }
}

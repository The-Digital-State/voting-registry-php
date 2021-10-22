<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the PollResult "saving" event.
     *
     * @param \App\Models\User $user
     * @return void
     * @throws \Exception
     */
    public function saving(User $user)
    {
        if (!$user->password) {
            $user->password = bcrypt(bin2hex(random_bytes(8)));
        }
    }
}

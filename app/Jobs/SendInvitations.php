<?php

namespace App\Jobs;

use App\Models\Invitation;
use App\Models\Poll;
use App\Notifications\InvitationCreated;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class SendInvitations extends Job
{
    /** @var Poll */
    protected $poll;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Poll $poll)
    {
        $this->poll = $poll->withoutRelations();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->poll->emailsList->emails as $email) {
            $invitation = Invitation::create([
                'token' => (string)Str::uuid(),
                'email' => $email,
                'poll_id' => $this->poll->id,
            ]);

            Notification::route('mail', $email)
                ->notify(new InvitationCreated($invitation));
        }
    }
}

<?php

namespace App\Jobs;

use App\Models\EmailsList;
use App\Models\Invitation;
use App\Models\Poll;
use App\Notifications\InvitationCreated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class InvitationsSend implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
            $invitation = new Invitation();
            $invitation->email = $email;
            $invitation->poll_id = $this->poll->id;
            $invitation->save();

            Notification::route('mail', $email)
                ->notify(new InvitationCreated($invitation));
        }
    }
}

<?php

namespace App\Providers;

use App\Models\Invitation;
use App\Models\Poll;
use App\Models\PollResult;
use App\Models\Voter;
use App\Models\User;
use App\Observers\InvitationObserver;
use App\Observers\PollObserver;
use App\Observers\PollResultObserver;
use App\Observers\VoterObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        // Observers
        User::observe(UserObserver::class);
        Invitation::observe(InvitationObserver::class);
        Poll::observe(PollObserver::class);
        PollResult::observe(PollResultObserver::class);
    }
}

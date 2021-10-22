<?php

namespace App\Providers;

use App\Models\EmailsList;
use App\Models\Poll;
use App\Policies\EmailsListPolicy;
use App\Policies\PollPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        EmailsList::class => EmailsListPolicy::class,
        Poll::class => PollPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}

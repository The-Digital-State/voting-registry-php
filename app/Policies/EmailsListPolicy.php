<?php

namespace App\Policies;

use App\Models\EmailsList;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class EmailsListPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can get list of models.
     *
     * @return Response|bool
     */
    public function list(): Response|bool
    {
        return true;
    }

    /**
     * Determine whether the user can get the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\EmailsList $emailsList
     * @return Response|bool
     */
    public function get(User $user, EmailsList $emailsList): Response|bool
    {
        return ($user->id === $emailsList->owner_id);
    }

    /**
     * Determine whether the user can create models.
     *
     * @return Response|bool
     */
    public function create(): Response|bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\EmailsList $emailsList
     * @return Response|bool
     */
    public function update(User $user, EmailsList $emailsList): Response|bool
    {
        return ($user->id === $emailsList->owner_id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\EmailsList $emailsList
     * @return Response|bool
     */
    public function delete(User $user, EmailsList $emailsList): Response|bool
    {
        $emailsList->loadCount('polls');

        if ($emailsList->polls_count) {
            return Response::deny('Unable to delete the list of emails that is participating in the vote');
        }

        return ($user->id === $emailsList->owner_id);
    }
}

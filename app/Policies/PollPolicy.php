<?php

namespace App\Policies;

use App\Models\Poll;
use App\Models\PollVoter;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class PollPolicy
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
     * @param \App\Models\Poll $poll
     * @return Response|bool
     */
    public function get(User $user, Poll $poll): Response|bool
    {
        return ($user->id === $poll->owner_id);
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
     * @param \App\Models\Poll $poll
     * @return Response|bool
     */
    public function update(User $user, Poll $poll): Response|bool
    {
        return (!$poll->published_at && $user->id === $poll->owner_id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Poll $poll
     * @return Response|bool
     */
    public function delete(User $user, Poll $poll): Response|bool
    {
        if ($poll->isInVoting()) {
            return Response::deny('It is forbidden to delete a poll at the time of voting');
        }

        return ($user->id === $poll->owner_id);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Poll $poll
     * @return Response|bool
     */
    public function restore(User $user, Poll $poll): Response|bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Poll $poll
     * @return Response|bool
     */
    public function forceDelete(User $user, Poll $poll): Response|bool
    {
        return ($user->id === $poll->owner_id && $poll->published_at === null);
    }

    /**
     * Determine whether the user can vote on a poll.
     *
     * @param User $user
     * @param Poll $poll
     * @return Response|bool
     */
    public function vote(User $user, Poll $poll): Response|bool
    {
        if (!$poll->isInVoting()) {
            print_r('invoting');
            return false;
        }

        if (!in_array($user->email, $poll->emailsList->emails)) {
            print_r('in_array');
            return false;
        }

        return !PollVoter::whereVoterId($user->id)->wherePollId($poll->id)->first();
    }
}

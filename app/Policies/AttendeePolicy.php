<?php

namespace App\Policies;

use App\Models\Attendee;
use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AttendeePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    // public function viewAny(User $user): bool
    // {
    //     //
    // }

    // /**
    //  * Determine whether the user can view the model.
    //  */
    // public function view(User $user, Attendee $attendee): bool
    // {
    //     //
    // }

    // /**
    //  * Determine whether the user can create models.
    //  */
    // public function create(User $user): bool
    // {
    //     //
    // }

    // /**
    //  * Determine whether the user can update the model.
    //  */
    // public function update(User $user, Attendee $attendee): bool
    // {
    //     //
    // }

    // /**
    //  * Determine whether the user can delete the model.
    //  */
    // public function delete(User $user, Attendee $attendee): bool
    // {
    //     //
    // }

    // /**
    //  * Determine whether the user can restore the model.
    //  */
    // public function restore(User $user, Attendee $attendee): bool
    // {
    //     //
    // }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function modify(User $user,Event $event, Attendee $attendee): Response
    {
        return $user->id === $event->user_id || $user->id === $attendee->user_id
        ? Response::allow()
        : Response::deny('You do not own this post');
    }
}

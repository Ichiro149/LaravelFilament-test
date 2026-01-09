<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any tickets.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the ticket.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        // Admins and assigned staff can view any ticket
        if ($user->role === 'admin' || $ticket->assigned_to === $user->id) {
            return true;
        }

        // Users can only view their own tickets
        return $ticket->user_id === $user->id;
    }

    /**
     * Determine whether the user can create tickets.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the ticket.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        // Admins can update any ticket
        if ($user->role === 'admin') {
            return true;
        }

        // Assigned staff can update
        if ($ticket->assigned_to === $user->id) {
            return true;
        }

        // Owner can only close their own ticket
        return $ticket->user_id === $user->id && $ticket->status !== 'closed';
    }

    /**
     * Determine whether the user can reply to the ticket.
     */
    public function reply(User $user, Ticket $ticket): bool
    {
        // Cannot reply to closed tickets
        if ($ticket->status === 'closed') {
            return false;
        }

        // Admins and assigned staff can reply
        if ($user->role === 'admin' || $ticket->assigned_to === $user->id) {
            return true;
        }

        // Owner can reply to their own ticket
        return $ticket->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the ticket.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        // Only admins can delete tickets
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can close the ticket.
     */
    public function close(User $user, Ticket $ticket): bool
    {
        // Admins can close any ticket
        if ($user->role === 'admin') {
            return true;
        }

        // Assigned staff can close
        if ($ticket->assigned_to === $user->id) {
            return true;
        }

        // Owner can close their own ticket
        return $ticket->user_id === $user->id;
    }
}

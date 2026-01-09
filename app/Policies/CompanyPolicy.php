<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any companies.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the company.
     */
    public function view(User $user, Company $company): bool
    {
        // Anyone can view active companies
        if ($company->is_active) {
            return true;
        }

        // Admins can view any company
        if ($user->role === 'admin') {
            return true;
        }

        // Owner can view their own company
        return $company->user_id === $user->id;
    }

    /**
     * Determine whether the user can create companies.
     */
    public function create(User $user): bool
    {
        // Only sellers and admins can create companies
        return in_array($user->role, ['seller', 'admin']);
    }

    /**
     * Determine whether the user can update the company.
     */
    public function update(User $user, Company $company): bool
    {
        // Admins can update any company
        if ($user->role === 'admin') {
            return true;
        }

        // Only owner can update their company
        return $company->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the company.
     */
    public function delete(User $user, Company $company): bool
    {
        // Admins can delete any company
        if ($user->role === 'admin') {
            return true;
        }

        // Owner can delete their own company (if no active orders)
        return $company->user_id === $user->id;
    }

    /**
     * Determine whether the user can verify the company.
     */
    public function verify(User $user, Company $company): bool
    {
        return $user->role === 'admin';
    }
}

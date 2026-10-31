<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Career;
use Illuminate\Auth\Access\HandlesAuthorization;

class CareerPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Career');
    }

    public function view(AuthUser $authUser, Career $career): bool
    {
        return $authUser->can('View:Career');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Career');
    }

    public function update(AuthUser $authUser, Career $career): bool
    {
        return $authUser->can('Update:Career');
    }

    public function delete(AuthUser $authUser, Career $career): bool
    {
        return $authUser->can('Delete:Career');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Career');
    }

    public function restore(AuthUser $authUser, Career $career): bool
    {
        return $authUser->can('Restore:Career');
    }

    public function forceDelete(AuthUser $authUser, Career $career): bool
    {
        return $authUser->can('ForceDelete:Career');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Career');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Career');
    }

    public function replicate(AuthUser $authUser, Career $career): bool
    {
        return $authUser->can('Replicate:Career');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Career');
    }

}
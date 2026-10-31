<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\NavLink;
use Illuminate\Auth\Access\HandlesAuthorization;

class NavLinkPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:NavLink');
    }

    public function view(AuthUser $authUser, NavLink $navLink): bool
    {
        return $authUser->can('View:NavLink');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:NavLink');
    }

    public function update(AuthUser $authUser, NavLink $navLink): bool
    {
        return $authUser->can('Update:NavLink');
    }

    public function delete(AuthUser $authUser, NavLink $navLink): bool
    {
        return $authUser->can('Delete:NavLink');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:NavLink');
    }

    public function restore(AuthUser $authUser, NavLink $navLink): bool
    {
        return $authUser->can('Restore:NavLink');
    }

    public function forceDelete(AuthUser $authUser, NavLink $navLink): bool
    {
        return $authUser->can('ForceDelete:NavLink');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:NavLink');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:NavLink');
    }

    public function replicate(AuthUser $authUser, NavLink $navLink): bool
    {
        return $authUser->can('Replicate:NavLink');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:NavLink');
    }

}
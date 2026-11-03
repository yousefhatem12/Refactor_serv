<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\FooterLink;
use Illuminate\Auth\Access\HandlesAuthorization;

class FooterLinkPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FooterLink');
    }

    public function view(AuthUser $authUser, FooterLink $footerLink): bool
    {
        return $authUser->can('View:FooterLink');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FooterLink');
    }

    public function update(AuthUser $authUser, FooterLink $footerLink): bool
    {
        return $authUser->can('Update:FooterLink');
    }

    public function delete(AuthUser $authUser, FooterLink $footerLink): bool
    {
        return $authUser->can('Delete:FooterLink');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:FooterLink');
    }

    public function restore(AuthUser $authUser, FooterLink $footerLink): bool
    {
        return $authUser->can('Restore:FooterLink');
    }

    public function forceDelete(AuthUser $authUser, FooterLink $footerLink): bool
    {
        return $authUser->can('ForceDelete:FooterLink');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FooterLink');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FooterLink');
    }

    public function replicate(AuthUser $authUser, FooterLink $footerLink): bool
    {
        return $authUser->can('Replicate:FooterLink');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FooterLink');
    }

}
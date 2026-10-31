<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SocialLink;
use Illuminate\Auth\Access\HandlesAuthorization;

class SocialLinkPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SocialLink');
    }

    public function view(AuthUser $authUser, SocialLink $socialLink): bool
    {
        return $authUser->can('View:SocialLink');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SocialLink');
    }

    public function update(AuthUser $authUser, SocialLink $socialLink): bool
    {
        return $authUser->can('Update:SocialLink');
    }

    public function delete(AuthUser $authUser, SocialLink $socialLink): bool
    {
        return $authUser->can('Delete:SocialLink');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:SocialLink');
    }

    public function restore(AuthUser $authUser, SocialLink $socialLink): bool
    {
        return $authUser->can('Restore:SocialLink');
    }

    public function forceDelete(AuthUser $authUser, SocialLink $socialLink): bool
    {
        return $authUser->can('ForceDelete:SocialLink');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SocialLink');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SocialLink');
    }

    public function replicate(AuthUser $authUser, SocialLink $socialLink): bool
    {
        return $authUser->can('Replicate:SocialLink');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SocialLink');
    }

}
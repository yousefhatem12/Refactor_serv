<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AiCredential;
use Illuminate\Auth\Access\HandlesAuthorization;

class AiCredentialPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AiCredential');
    }

    public function view(AuthUser $authUser, AiCredential $aiCredential): bool
    {
        return $authUser->can('View:AiCredential');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AiCredential');
    }

    public function update(AuthUser $authUser, AiCredential $aiCredential): bool
    {
        return $authUser->can('Update:AiCredential');
    }

    public function delete(AuthUser $authUser, AiCredential $aiCredential): bool
    {
        return $authUser->can('Delete:AiCredential');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AiCredential');
    }

    public function restore(AuthUser $authUser, AiCredential $aiCredential): bool
    {
        return $authUser->can('Restore:AiCredential');
    }

    public function forceDelete(AuthUser $authUser, AiCredential $aiCredential): bool
    {
        return $authUser->can('ForceDelete:AiCredential');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AiCredential');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AiCredential');
    }

    public function replicate(AuthUser $authUser, AiCredential $aiCredential): bool
    {
        return $authUser->can('Replicate:AiCredential');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AiCredential');
    }

}
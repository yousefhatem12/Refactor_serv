<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ProjectConsultation;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectConsultationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProjectConsultation');
    }

    public function view(AuthUser $authUser, ProjectConsultation $projectConsultation): bool
    {
        return $authUser->can('View:ProjectConsultation');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProjectConsultation');
    }

    public function update(AuthUser $authUser, ProjectConsultation $projectConsultation): bool
    {
        return $authUser->can('Update:ProjectConsultation');
    }

    public function delete(AuthUser $authUser, ProjectConsultation $projectConsultation): bool
    {
        return $authUser->can('Delete:ProjectConsultation');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ProjectConsultation');
    }

    public function restore(AuthUser $authUser, ProjectConsultation $projectConsultation): bool
    {
        return $authUser->can('Restore:ProjectConsultation');
    }

    public function forceDelete(AuthUser $authUser, ProjectConsultation $projectConsultation): bool
    {
        return $authUser->can('ForceDelete:ProjectConsultation');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProjectConsultation');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProjectConsultation');
    }

    public function replicate(AuthUser $authUser, ProjectConsultation $projectConsultation): bool
    {
        return $authUser->can('Replicate:ProjectConsultation');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProjectConsultation');
    }

}
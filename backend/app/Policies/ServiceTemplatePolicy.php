<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ServiceTemplate;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServiceTemplatePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ServiceTemplate');
    }

    public function view(AuthUser $authUser, ServiceTemplate $serviceTemplate): bool
    {
        return $authUser->can('View:ServiceTemplate');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ServiceTemplate');
    }

    public function update(AuthUser $authUser, ServiceTemplate $serviceTemplate): bool
    {
        return $authUser->can('Update:ServiceTemplate');
    }

    public function delete(AuthUser $authUser, ServiceTemplate $serviceTemplate): bool
    {
        return $authUser->can('Delete:ServiceTemplate');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ServiceTemplate');
    }

    public function restore(AuthUser $authUser, ServiceTemplate $serviceTemplate): bool
    {
        return $authUser->can('Restore:ServiceTemplate');
    }

    public function forceDelete(AuthUser $authUser, ServiceTemplate $serviceTemplate): bool
    {
        return $authUser->can('ForceDelete:ServiceTemplate');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ServiceTemplate');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ServiceTemplate');
    }

    public function replicate(AuthUser $authUser, ServiceTemplate $serviceTemplate): bool
    {
        return $authUser->can('Replicate:ServiceTemplate');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ServiceTemplate');
    }

}
<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CareerApplication;
use Illuminate\Auth\Access\HandlesAuthorization;

class CareerApplicationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CareerApplication');
    }

    public function view(AuthUser $authUser, CareerApplication $careerApplication): bool
    {
        return $authUser->can('View:CareerApplication');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CareerApplication');
    }

    public function update(AuthUser $authUser, CareerApplication $careerApplication): bool
    {
        return $authUser->can('Update:CareerApplication');
    }

    public function delete(AuthUser $authUser, CareerApplication $careerApplication): bool
    {
        return $authUser->can('Delete:CareerApplication');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CareerApplication');
    }

    public function restore(AuthUser $authUser, CareerApplication $careerApplication): bool
    {
        return $authUser->can('Restore:CareerApplication');
    }

    public function forceDelete(AuthUser $authUser, CareerApplication $careerApplication): bool
    {
        return $authUser->can('ForceDelete:CareerApplication');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CareerApplication');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CareerApplication');
    }

    public function replicate(AuthUser $authUser, CareerApplication $careerApplication): bool
    {
        return $authUser->can('Replicate:CareerApplication');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CareerApplication');
    }

}
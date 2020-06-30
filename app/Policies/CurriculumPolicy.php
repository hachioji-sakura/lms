<?php

namespace App\Policies;

use App\User;
use App\Models\Curriculum;
use Illuminate\Auth\Access\HandlesAuthorization;

class CurriculumPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the curriculum.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Curriculum  $curriculum
     * @return mixed
     */
    public function view(User $user, Curriculum $curriculum)
    {
        //
        return $user->details()->role == "manager";
    }

    public function viewAny(User $user, Curriculum $curriculum)
    {
        //
        return $user->details()->role == "manager";
    }

    /**
     * Determine whether the user can create curricula.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
        return $user->details()->role == "manager";
    }

    /**
     * Determine whether the user can update the curriculum.
     *
     * @param  \App\User  $user
     * @param  \App\Curriculum  $curriculum
     * @return mixed
     */
    public function update(User $user, Curriculum $curriculum)
    {
        //
        return $user->details()->role == "manager";
    }

    /**
     * Determine whether the user can delete the curriculum.
     *
     * @param  \App\User  $user
     * @param  \App\Curriculum  $curriculum
     * @return mixed
     */
    public function delete(User $user, Curriculum $curriculum)
    {
        //
        return $user->details()->role == "manager";
    }

}

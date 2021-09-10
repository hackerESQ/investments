<?php

namespace App\Policies;

use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PortfolioPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Portfolio  $portfolio
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Portfolio $portfolio)
    {
        return $portfolio->users->contains('id', $user->id);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Portfolio  $portfolio
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Portfolio $portfolio)
    {
        return $portfolio->users->contains('id', $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Portfolio  $portfolio
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Portfolio $portfolio)
    {
        return $portfolio->users->contains('id', $user->id);
    }
}

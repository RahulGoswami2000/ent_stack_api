<?php

namespace App\Observers;

use App\Models\TeamStack;

class TeamStackObserver
{
    /**
     * Handle the TeamStack "created" event.
     *
     * @param  \App\Models\TeamStack  $teamStack
     * @return void
     */
    public function creating(TeamStack $teamStack)
    {
        $teamStack->created_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $teamStack->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }
    /**
     * Handle the TeamStack "created" event.
     *
     * @param  \App\Models\TeamStack  $teamStack
     * @return void
     */
    public function created(TeamStack $teamStack)
    {
        //
    }

    /**
     * Handle the TeamStack "updated" event.
     *
     * @param  \App\Models\TeamStack  $teamStack
     * @return void
     */

    public function updating(TeamStack $teamStack)
    {
        $teamStack->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }

    public function updated(TeamStack $teamStack)
    {
        //
    }

    /**
     * Handle the TeamStack "deleted" event.
     *
     * @param  \App\Models\TeamStack  $teamStack
     * @return void
     */

    public function deleting(TeamStack $teamStack)
    {
        $teamStack->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $teamStack->save();
    }

    public function deleted(TeamStack $teamStack)
    {
        $teamStack->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }

    /**
     * Handle the TeamStack "restored" event.
     *
     * @param  \App\Models\TeamStack  $teamStack
     * @return void
     */
    public function restored(TeamStack $teamStack)
    {
        //
    }

    /**
     * Handle the TeamStack "force deleted" event.
     *
     * @param  \App\Models\TeamStack  $teamStack
     * @return void
     */
    public function forceDeleted(TeamStack $teamStack)
    {
        //
    }
}

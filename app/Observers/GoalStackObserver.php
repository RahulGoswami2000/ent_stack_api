<?php

namespace App\Observers;

use App\Models\GoalStack;

class GoalStackObserver
{

    /**
     * Handle the Subscription "created" event.
     *
     * @param  \App\Models\GoalStack  $goalStack
     * @return void
     */

    public function creating(GoalStack $goalStack)
    {
        $goalStack->created_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $goalStack->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }

    /**
     * Handle the GoalStack "created" event.
     *
     * @param  \App\Models\GoalStack  $goalStack
     * @return void
     */
    public function created(GoalStack $goalStack)
    {
        //
    }

    public function updating(GoalStack $goalStack)
    {
        $goalStack->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }

    /**
     * Handle the GoalStack "updated" event.
     *
     * @param  \App\Models\GoalStack  $goalStack
     * @return void
     */
    public function updated(GoalStack $goalStack)
    {
        //
    }

    public function deleting(GoalStack $goalStack)
    {
        // echo Auth::user()->id; die();
        $goalStack->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $goalStack->save();
        // print_r($subscription); die();
    }

    /**
     * Handle the GoalStack "deleted" event.
     *
     * @param  \App\Models\GoalStack  $goalStack
     * @return void
     */
    public function deleted(GoalStack $goalStack)
    {
        //
    }

    /**
     * Handle the GoalStack "restored" event.
     *
     * @param  \App\Models\GoalStack  $goalStack
     * @return void
     */
    public function restored(GoalStack $goalStack)
    {
        //
    }

    /**
     * Handle the GoalStack "force deleted" event.
     *
     * @param  \App\Models\GoalStack  $goalStack
     * @return void
     */
    public function forceDeleted(GoalStack $goalStack)
    {
        //
    }
}

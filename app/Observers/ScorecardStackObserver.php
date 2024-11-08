<?php

namespace App\Observers;

use App\Models\ScorecardStack;

class ScorecardStackObserver
{
    /**
     * Handle the ScorecardStack "created" event.
     *
     * @param  \App\Models\ScorecardStack  $scorecardStack
     * @return void
     */
    public function creating(ScorecardStack $scorecardStack)
    {
        $scorecardStack->created_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $scorecardStack->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }
    /**
     * Handle the ScorecardStack "created" event.
     *
     * @param  \App\Models\ScorecardStack  $scorecardStack
     * @return void
     */
    public function created(ScorecardStack $scorecardStack)
    {
        //
    }
    /**
     * Handle the ScorecardStack "updated" event.
     *
     * @param  \App\Models\ScorecardStack  $scorecardStack
     * @return void
     */
    public function updating(ScorecardStack $scorecardStack)
    {
        $scorecardStack->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }
    /**
     * Handle the ScorecardStack "updated" event.
     *
     * @param  \App\Models\ScorecardStack  $scorecardStack
     * @return void
     */
    public function updated(ScorecardStack $scorecardStack)
    {
        //
    }

    /**
     * Handle the ScorecardStack "deleted" event.
     *
     * @param  \App\Models\ScorecardStack  $scorecardStack
     * @return void
     */
    public function deleting(ScorecardStack $scorecardStack)
    {
        $scorecardStack->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $scorecardStack->save();
    }
    /**
     * Handle the ScorecardStack "deleted" event.
     *
     * @param  \App\Models\ScorecardStack  $scorecardStack
     * @return void
     */
    public function deleted(ScorecardStack $scorecardStack)
    {
        //
    }

    /**
     * Handle the ScorecardStack "restored" event.
     *
     * @param  \App\Models\ScorecardStack  $scorecardStack
     * @return void
     */
    public function restored(ScorecardStack $scorecardStack)
    {
        //
    }

    /**
     * Handle the ScorecardStack "force deleted" event.
     *
     * @param  \App\Models\ScorecardStack  $scorecardStack
     * @return void
     */
    public function forceDeleted(ScorecardStack $scorecardStack)
    {
        //
    }
}

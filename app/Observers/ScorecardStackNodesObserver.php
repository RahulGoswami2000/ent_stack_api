<?php

namespace App\Observers;

use App\Models\ScorecardStackNodes;

class ScorecardStackNodesObserver
{
    /**
     * Handle the ScorecardStackNodes "created" event.
     *
     * @param  \App\Models\ScorecardStackNodes  $scorecardStackNodes
     * @return void
     */
    public function creating(ScorecardStackNodes $scorecardStackNodes)
    {
        $scorecardStackNodes->created_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $scorecardStackNodes->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }

    /**
     * Handle the ScorecardStack "created" event.
     *
     * @param  \App\Models\ScorecardStackNodes  $scorecardStack
     * @return void
     */
    public function created(ScorecardStackNodes $scorecardStack)
    {
        //
    }

    /**
     * Handle the ScorecardStack "updated" event.
     *
     * @param  \App\Models\ScorecardStackNodes  $scorecardStack
     * @return void
     */
    public function updating(ScorecardStackNodes $scorecardStackNodes)
    {
        $scorecardStackNodes->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }

    /**
     * Handle the ScorecardStackNodes "updated" event.
     *
     * @param  \App\Models\ScorecardStackNodes  $scorecardStackNodes
     * @return void
     */
    public function updated(ScorecardStackNodes $scorecardStackNodes)
    {
        //
    }

    /**
     * Handle the ScorecardStack "deleted" event.
     *
     * @param  \App\Models\ScorecardStackNodes  $scorecardStack
     * @return void
     */
    public function deleting(ScorecardStackNodes $scorecardStackNodes)
    {
        $scorecardStackNodes->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $scorecardStackNodes->save();
    }

    /**
     * Handle the ScorecardStackNodes "deleted" event.
     *
     * @param  \App\Models\ScorecardStackNodes  $scorecardStackNodes
     * @return void
     */
    public function deleted(ScorecardStackNodes $scorecardStackNodes)
    {
        //
    }

    /**
     * Handle the ScorecardStackNodes "restored" event.
     *
     * @param  \App\Models\ScorecardStackNodes  $scorecardStackNodes
     * @return void
     */
    public function restored(ScorecardStackNodes $scorecardStackNodes)
    {
        //
    }

    /**
     * Handle the ScorecardStackNodes "force deleted" event.
     *
     * @param  \App\Models\ScorecardStackNodes  $scorecardStackNodes
     * @return void
     */
    public function forceDeleted(ScorecardStackNodes $scorecardStackNodes)
    {
        //
    }
}

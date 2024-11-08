<?php

namespace App\Observers;

use App\Models\ScorecardStackNodeData;

class ScorecardStackNodeDataObserver
{

    /**
     * Handle the Subscription "created" event.
     *
     * @param  \App\Models\ScorecardStackNodeData  $scorecardStackNodeData
     * @return void
     */

    public function creating(ScorecardStackNodeData $scorecardStackNodeData)
    {
        $scorecardStackNodeData->created_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $scorecardStackNodeData->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }


    /**
     * Handle the ScorecardStackNodeData "created" event.
     *
     * @param  \App\Models\ScorecardStackNodeData  $scorecardStackNodeData
     * @return void
     */
    public function created(ScorecardStackNodeData $scorecardStackNodeData)
    {
        //
    }

    public function updating(ScorecardStackNodeData $scorecardStackNodeData)
    {
        $scorecardStackNodeData->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }

    /**
     * Handle the ScorecardStackNodeData "updated" event.
     *
     * @param  \App\Models\ScorecardStackNodeData  $scorecardStackNodeData
     * @return void
     */
    public function updated(ScorecardStackNodeData $scorecardStackNodeData)
    {
        //
    }

    /**
     * Handle the Subscription "deleted" event.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return void
     */
    public function deleting(ScorecardStackNodeData $scorecardStackNodeData)
    {
        // echo Auth::user()->id; die();
        $scorecardStackNodeData->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $scorecardStackNodeData->save();
        // print_r($subscription); die();
    }

    /**
     * Handle the ScorecardStackNodeData "deleted" event.
     *
     * @param  \App\Models\ScorecardStackNodeData  $scorecardStackNodeData
     * @return void
     */
    public function deleted(ScorecardStackNodeData $scorecardStackNodeData)
    {
        //
    }

    /**
     * Handle the ScorecardStackNodeData "restored" event.
     *
     * @param  \App\Models\ScorecardStackNodeData  $scorecardStackNodeData
     * @return void
     */
    public function restored(ScorecardStackNodeData $scorecardStackNodeData)
    {
        //
    }

    /**
     * Handle the ScorecardStackNodeData "force deleted" event.
     *
     * @param  \App\Models\ScorecardStackNodeData  $scorecardStackNodeData
     * @return void
     */
    public function forceDeleted(ScorecardStackNodeData $scorecardStackNodeData)
    {
        //
    }
}

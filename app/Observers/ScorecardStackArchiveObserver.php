<?php

namespace App\Observers;

use App\Models\ScorecardStackArchive;

class ScorecardStackArchiveObserver
{
    /**
     * Handle the ScorecardStackArchive "created" event.
     *
     * @param  \App\Models\ScorecardStackArchive  $scorecardStackArchive
     * @return void
     */
    public function creating(ScorecardStackArchive $scorecardStackArchive)
    {
        $scorecardStackArchive->created_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $scorecardStackArchive->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }

    /**
     * Handle the ScorecardStackArchive "updated" event.
     *
     * @param  \App\Models\ScorecardStackArchive  $scorecardStackArchive
     * @return void
     */
    public function created(ScorecardStackArchive $scorecardStackArchive)
    {
        //
    }

    /**
     * Handle the ScorecardStack "updated" event.
     *
     * @param  \App\Models\ScorecardStackArchive  $scorecardStackArchive
     * @return void
     */
    public function updating(ScorecardStackArchive $scorecardStackArchive)
    {
        $scorecardStackArchive->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }

    /**
     * Handle the ScorecardStackArchive "updated" event.
     *
     * @param  \App\Models\ScorecardStackArchive  $scorecardStackArchive
     * @return void
     */
    public function updated(ScorecardStackArchive $scorecardStackArchive)
    {
        //
    }

    /**
     * Handle the ScorecardStack "deleted" event.
     *
     * @param  \App\Models\ScorecardStackArchive  $scorecardStackArchive
     * @return void
     */
    public function deleting(ScorecardStackArchive $scorecardStackArchive)
    {
        $scorecardStackArchive->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $scorecardStackArchive->save();
    }

    /**
     * Handle the ScorecardStackArchive "deleted" event.
     *
     * @param  \App\Models\ScorecardStackArchive  $scorecardStackArchive
     * @return void
     */
    public function deleted(ScorecardStackArchive $scorecardStackArchive)
    {
        //
    }

    /**
     * Handle the ScorecardStackArchive "restored" event.
     *
     * @param  \App\Models\ScorecardStackArchive  $scorecardStackArchive
     * @return void
     */
    public function restored(ScorecardStackArchive $scorecardStackArchive)
    {
        //
    }

    /**
     * Handle the ScorecardStackArchive "force deleted" event.
     *
     * @param  \App\Models\ScorecardStackArchive  $scorecardStackArchive
     * @return void
     */
    public function forceDeleted(ScorecardStackArchive $scorecardStackArchive)
    {
        //
    }
}

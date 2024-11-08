<?php

namespace App\Observers;

use App\Models\UserStackAccess;

class UserStackAccessObserver
{

    /**
     * Handle the ScorecardStack "created" event.
     *
     * @param  \App\Models\UserStackAccess  $scorecardStack
     * @return void
     */
    public function creating(UserStackAccess $userStackAccess)
    {
        $userStackAccess->created_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $userStackAccess->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }
    /**
     * Handle the UserStackAccess "created" event.
     *
     * @param  \App\Models\UserStackAccess  $userStackAccess
     * @return void
     */
    public function created(UserStackAccess $userStackAccess)
    {
        //
    }

    /**
     * Handle the ScorecardStack "updated" event.
     *
     * @param  \App\Models\UserStackAccess  $scorecardStack
     * @return void
     */
    public function updating(UserStackAccess $userStackAccess)
    {
        $userStackAccess->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }

    /**
     * Handle the UserStackAccess "updated" event.
     *
     * @param  \App\Models\UserStackAccess  $userStackAccess
     * @return void
     */
    public function updated(UserStackAccess $userStackAccess)
    {
        //
    }

    /**
     * Handle the ScorecardStack "deleted" event.
     *
     * @param  \App\Models\UserStackAccess  $userStackAccess
     * @return void
     */
    public function deleting(UserStackAccess $userStackAccess)
    {
        $userStackAccess->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $userStackAccess->save();
    }

    /**
     * Handle the UserStackAccess "deleted" event.
     *
     * @param  \App\Models\UserStackAccess  $userStackAccess
     * @return void
     */
    public function deleted(UserStackAccess $userStackAccess)
    {
        //
        // $userStackAccess->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        // $userStackAccess->save();
    }

    /**
     * Handle the UserStackAccess "restored" event.
     *
     * @param  \App\Models\UserStackAccess  $userStackAccess
     * @return void
     */
    public function restored(UserStackAccess $userStackAccess)
    {
        //
    }

    /**
     * Handle the UserStackAccess "force deleted" event.
     *
     * @param  \App\Models\UserStackAccess  $userStackAccess
     * @return void
     */
    public function forceDeleted(UserStackAccess $userStackAccess)
    {
        //
    }
}

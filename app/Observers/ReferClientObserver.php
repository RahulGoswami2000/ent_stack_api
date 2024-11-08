<?php

namespace App\Observers;

use App\Models\ReferClient;

class ReferClientObserver
{
    /**
     * Handle the ReferClient "created" event.
     *
     * @param  \App\Models\ReferClient  $referClient
     * @return void
     */
    public function creating(ReferClient $referClient)
    {
        $referClient->created_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $referClient->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }
    /**
     * Handle the ReferClient "created" event.
     *
     * @param  \App\Models\ReferClient  $referClient
     * @return void
     */
    public function created(ReferClient $referClient)
    {
        //
    }
    /**
     * Handle the ReferClient "updated" event.
     *
     * @param  \App\Models\ReferClient  $referClient
     * @return void
     */
    public function updating(ReferClient $referClient)
    {
        $referClient->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }
    /**
     * Handle the ReferClient "updated" event.
     *
     * @param  \App\Models\ReferClient  $referClient
     * @return void
     */
    public function updated(ReferClient $referClient)
    {
        //
    }

    /**
     * Handle the ReferClient "deleted" event.
     *
     * @param  \App\Models\ReferClient  $referClient
     * @return void
     */
    public function deleting(ReferClient $referClient)
    {
        $referClient->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $referClient->save();
    }
    /**
     * Handle the ReferClient "deleted" event.
     *
     * @param  \App\Models\ReferClient  $referClient
     * @return void
     */
    public function deleted(ReferClient $referClient)
    {
        //
    }

    /**
     * Handle the ReferClient "restored" event.
     *
     * @param  \App\Models\ReferClient  $referClient
     * @return void
     */
    public function restored(ReferClient $referClient)
    {
        //
    }

    /**
     * Handle the ReferClient "force deleted" event.
     *
     * @param  \App\Models\ReferClient  $referClient
     * @return void
     */
    public function forceDeleted(ReferClient $referClient)
    {
        //
    }
}

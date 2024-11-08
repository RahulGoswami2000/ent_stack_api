<?php

namespace App\Observers;

use App\Models\SubscriptionHistory;

class SubscriptionHistoryObserver
{

    /**
     * Handle the SubscriptionHistory "created" event.
     *
     * @param  \App\Models\SubscriptionHistory  $subscriptionHistory
     * @return void
     */
    public function creating(SubscriptionHistory $subscriptionHistory)
    {
        $subscriptionHistory->created_by = (!empty(\Auth::user()) && \Auth::user()->id)? \Auth::user()->id: NULL;
        $subscriptionHistory->updated_by = (!empty(\Auth::user()) && \Auth::user()->id)? \Auth::user()->id: NULL;
    }

    /**
     * Handle the SubscriptionHistory "created" event.
     *
     * @param  \App\Models\SubscriptionHistory  $subscriptionHistory
     * @return void
     */
    public function created(SubscriptionHistory $subscriptionHistory)
    {
        //
    }

    /**
     * Handle the SubscriptionHistory "updated" event.
     *
     * @param  \App\Models\SubscriptionHistory  $subscriptionHistory
     * @return void
     */
    public function updating(SubscriptionHistory $subscriptionHistory)
    {
        $subscriptionHistory->updated_by = (!empty(\Auth::user()) && \Auth::user()->id)? \Auth::user()->id: NULL;
    }

    /**
     * Handle the SubscriptionHistory "updated" event.
     *
     * @param  \App\Models\SubscriptionHistory  $subscriptionHistory
     * @return void
     */
    public function updated(SubscriptionHistory $subscriptionHistory)
    {
        //
    }

    /**
     * Handle the SubscriptionHistory "deleted" event.
     *
     * @param  \App\Models\SubscriptionHistory  $subscriptionHistory
     * @return void
     */
    public function deleting(SubscriptionHistory $subscriptionHistory)
    {
        $subscriptionHistory->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id)? \Auth::user()->id: NULL;
        $subscriptionHistory->save();
    }

    /**
     * Handle the SubscriptionHistory "deleted" event.
     *
     * @param  \App\Models\SubscriptionHistory  $subscriptionHistory
     * @return void
     */
    public function deleted(SubscriptionHistory $subscriptionHistory)
    {
        $subscriptionHistory->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id)? \Auth::user()->id: NULL;
    }

    /**
     * Handle the SubscriptionHistory "restored" event.
     *
     * @param  \App\Models\SubscriptionHistory  $subscriptionHistory
     * @return void
     */
    public function restored(SubscriptionHistory $subscriptionHistory)
    {
        //
    }

    /**
     * Handle the SubscriptionHistory "force deleted" event.
     *
     * @param  \App\Models\SubscriptionHistory  $subscriptionHistory
     * @return void
     */
    public function forceDeleted(SubscriptionHistory $subscriptionHistory)
    {
        //
    }
}

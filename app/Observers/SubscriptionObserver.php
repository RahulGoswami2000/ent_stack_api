<?php

namespace App\Observers;

use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;

class SubscriptionObserver
{

    /**
     * Handle the Subscription "created" event.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return void
     */

    public function creating(Subscription $subscription) {
        $subscription->created_by = (!empty(\Auth::user()) && \Auth::user()->id)? \Auth::user()->id: NULL;
        $subscription->updated_by = (!empty(\Auth::user()) && \Auth::user()->id)? \Auth::user()->id: NULL;
    }

    /**
     * Handle the Subscription "created" event.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return void
     */
    public function created(Subscription $subscription)
    {
        
    }

    public function updating(Subscription $subscription) {
        $subscription->updated_by = (!empty(\Auth::user()) && \Auth::user()->id)? \Auth::user()->id: NULL;
    }

    /**
     * Handle the Subscription "updated" event.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return void
     */
    public function updated(Subscription $subscription)
    {
        //
    }

    /**
     * Handle the Subscription "deleted" event.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return void
     */
    public function deleting(Subscription $subscription)
    {
        // echo Auth::user()->id; die();
        $subscription->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id)? \Auth::user()->id: NULL;
        $subscription->save();
        // print_r($subscription); die();
    }

    /**
     * Handle the Subscription "deleted" event.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return void
     */
    public function deleted(Subscription $subscription)
    {
        $subscription->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id)? \Auth::user()->id: NULL;
    }

    /**
     * Handle the Subscription "restored" event.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return void
     */
    public function restored(Subscription $subscription)
    {
        //
    }

    /**
     * Handle the Subscription "force deleted" event.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return void
     */
    public function forceDeleted(Subscription $subscription)
    {
        //
    }
}

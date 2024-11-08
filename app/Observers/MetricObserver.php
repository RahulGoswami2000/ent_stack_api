<?php

namespace App\Observers;

use App\Models\Metric;

class MetricObserver
{

    public function creating(Metric $metric) {
        $metric->created_by = (!empty(\Auth::user()) && \Auth::user()->id)? \Auth::user()->id: NULL;
        $metric->updated_by = (!empty(\Auth::user()) && \Auth::user()->id)? \Auth::user()->id: NULL;
    }
    /**
     * Handle the Metric "created" event.
     *
     * @param  \App\Models\Metric  $metric
     * @return void
     */
    public function created(Metric $metric)
    {
        //
    }

    /**
     * Handle the ScorecardStack "updated" event.
     *
     * @param  \App\Models\Metric  $scorecardStack
     * @return void
     */
    public function updating(Metric $metric)
    {
        $metric->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }

    /**
     * Handle the Metric "updated" event.
     *
     * @param  \App\Models\Metric  $metric
     * @return void
     */
    public function updated(Metric $metric)
    {
        //
    }


    public function deleting(Metric $metric)
    {
        // echo Auth::user()->id; die();
        $metric->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id)? \Auth::user()->id: NULL;
        $metric->save();
        // print_r($subscription); die();
    }

    /**
     * Handle the Metric "deleted" event.
     *
     * @param  \App\Models\Metric  $metric
     * @return void
     */
    public function deleted(Metric $metric)
    {
        $metric->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id)? \Auth::user()->id: NULL;
    }

    /**
     * Handle the Metric "restored" event.
     *
     * @param  \App\Models\Metric  $metric
     * @return void
     */
    public function restored(Metric $metric)
    {
        //
    }

    /**
     * Handle the Metric "force deleted" event.
     *
     * @param  \App\Models\Metric  $metric
     * @return void
     */
    public function forceDeleted(Metric $metric)
    {
        //
    }
}

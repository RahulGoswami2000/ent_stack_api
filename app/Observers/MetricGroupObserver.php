<?php

namespace App\Observers;

use App\Models\MetricGroup;

class MetricGroupObserver
{

    public function creating(MetricGroup $metricGroup) {
        $metricGroup->created_by = (!empty(\Auth::user()) && \Auth::user()->id)? \Auth::user()->id: NULL;
        $metricGroup->updated_by = (!empty(\Auth::user()) && \Auth::user()->id)? \Auth::user()->id: NULL;
    }

    /**
     * Handle the MetricGroup "created" event.
     *
     * @param  \App\Models\MetricGroup  $metricGroup
     * @return void
     */
    public function created(MetricGroup $metricGroup)
    {
        //
    }

    /**
     * Handle the ScorecardStack "updated" event.
     *
     * @param  \App\Models\MetricGroup  $scorecardStack
     * @return void
     */
    public function updating(MetricGroup $metricGroup)
    {
        $metricGroup->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }

    /**
     * Handle the MetricGroup "updated" event.
     *
     * @param  \App\Models\MetricGroup  $metricGroup
     * @return void
     */
    public function updated(MetricGroup $metricGroup)
    {
        //
    }

    public function deleting(MetricGroup $metricGroup)
    {
        $metricGroup->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id)? \Auth::user()->id: NULL;
        $metricGroup->save();
    }

    /**
     * Handle the MetricGroup "deleted" event.
     *
     * @param  \App\Models\MetricGroup  $metricGroup
     * @return void
     */
    public function deleted(MetricGroup $metricGroup)
    {
        $metricGroup->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id)? \Auth::user()->id: NULL;
    }

    /**
     * Handle the MetricGroup "restored" event.
     *
     * @param  \App\Models\MetricGroup  $metricGroup
     * @return void
     */
    public function restored(MetricGroup $metricGroup)
    {
        //
    }

    /**
     * Handle the MetricGroup "force deleted" event.
     *
     * @param  \App\Models\MetricGroup  $metricGroup
     * @return void
     */
    public function forceDeleted(MetricGroup $metricGroup)
    {
        //
    }
}

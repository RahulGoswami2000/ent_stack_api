<?php

namespace App\Observers;

use App\Models\MetricCategory;

class MetricCategoryObserver
{

    public function creating(MetricCategory $metricCategory)
    {
        $metricCategory->created_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $metricCategory->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }

    /**
     * Handle the MetricCategory "created" event.
     *
     * @param  \App\Models\MetricCategory  $metricCategory
     * @return void
     */
    public function created(MetricCategory $metricCategory)
    {
        //
    }

    /**
     * Handle the ScorecardStack "updated" event.
     *
     * @param  \App\Models\MetricCategory  $scorecardStack
     * @return void
     */
    public function updating(MetricCategory $metricCategory)
    {
        $metricCategory->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }

    /**
     * Handle the MetricCategory "updated" event.
     *
     * @param  \App\Models\MetricCategory  $metricCategory
     * @return void
     */
    public function updated(MetricCategory $metricCategory)
    {
        //
    }

    public function deleting(MetricCategory $metricCategory)
    {
        $metricCategory->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $metricCategory->save();
    }

    /**
     * Handle the MetricCategory "deleted" event.
     *
     * @param  \App\Models\MetricCategory  $metricCategory
     * @return void
     */
    public function deleted(MetricCategory $metricCategory)
    {
        //
        $metricCategory->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }

    /**
     * Handle the MetricCategory "restored" event.
     *
     * @param  \App\Models\MetricCategory  $metricCategory
     * @return void
     */
    public function restored(MetricCategory $metricCategory)
    {
        //
    }

    /**
     * Handle the MetricCategory "force deleted" event.
     *
     * @param  \App\Models\MetricCategory  $metricCategory
     * @return void
     */
    public function forceDeleted(MetricCategory $metricCategory)
    {
        //
    }
}

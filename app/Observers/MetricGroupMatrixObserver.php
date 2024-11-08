<?php

namespace App\Observers;

use App\Models\MetricGroupMatrix;

class MetricGroupMatrixObserver
{

    /**
     * Handle the ScorecardStack "created" event.
     *
     * @param  \App\Models\MetricGroupMatrix  $scorecardStack
     * @return void
     */
    public function creating(MetricGroupMatrix $metricGroupMatrix)
    {
        $metricGroupMatrix->created_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $metricGroupMatrix->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }
    /**
     * Handle the MetricGroupMatrix "created" event.
     *
     * @param  \App\Models\MetricGroupMatrix  $metricGroupMatrix
     * @return void
     */
    public function created(MetricGroupMatrix $metricGroupMatrix)
    {
        //
    }

    /**
     * Handle the ScorecardStack "updated" event.
     *
     * @param  \App\Models\MetricGroupMatrix  $metricGroupMatrix
     * @return void
     */
    public function updating(MetricGroupMatrix $metricGroupMatrix)
    {
        $metricGroupMatrix->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }

    /**
     * Handle the MetricGroupMatrix "updated" event.
     *
     * @param  \App\Models\MetricGroupMatrix  $metricGroupMatrix
     * @return void
     */
    public function updated(MetricGroupMatrix $metricGroupMatrix)
    {
        //
    }

    /**
     * Handle the ScorecardStack "deleted" event.
     *
     * @param  \App\Models\MetricGroupMatrix  $scorecardStack
     * @return void
     */
    public function deleting(MetricGroupMatrix $metricGroupMatrix)
    {
        $metricGroupMatrix->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $metricGroupMatrix->save();
    }

    /**
     * Handle the MetricGroupMatrix "deleted" event.
     *
     * @param  \App\Models\MetricGroupMatrix  $metricGroupMatrix
     * @return void
     */
    public function deleted(MetricGroupMatrix $metricGroupMatrix)
    {
        //
    }

    /**
     * Handle the MetricGroupMatrix "restored" event.
     *
     * @param  \App\Models\MetricGroupMatrix  $metricGroupMatrix
     * @return void
     */
    public function restored(MetricGroupMatrix $metricGroupMatrix)
    {
        //
    }

    /**
     * Handle the MetricGroupMatrix "force deleted" event.
     *
     * @param  \App\Models\MetricGroupMatrix  $metricGroupMatrix
     * @return void
     */
    public function forceDeleted(MetricGroupMatrix $metricGroupMatrix)
    {
        //
    }
}

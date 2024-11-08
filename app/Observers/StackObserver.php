<?php

namespace App\Observers;

use App\Models\Stack;

class StackObserver
{
    /**
     * Handle the ScorecardStack "created" event.
     *
     * @param  \App\Models\Stack  $scorecardStack
     * @return void
     */
    public function creating(Stack $stack)
    {
        $stack->created_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $stack->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }
    /**
     * Handle the Stack "created" event.
     *
     * @param  \App\Models\Stack  $stack
     * @return void
     */
    public function created(Stack $stack)
    {
        //
    }

    /**
     * Handle the ScorecardStack "updated" event.
     *
     * @param  \App\Models\Stack  $scorecardStack
     * @return void
     */
    public function updating(Stack $stack)
    {
        $stack->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }

    /**
     * Handle the Stack "updated" event.
     *
     * @param  \App\Models\Stack  $stack
     * @return void
     */
    public function updated(Stack $stack)
    {
        //
    }

    /**
     * Handle the ScorecardStack "deleted" event.
     *
     * @param  \App\Models\Stack  $stack
     * @return void
     */
    public function deleting(Stack $stack)
    {
        $stack->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $stack->save();
    }

    /**
     * Handle the Stack "deleted" event.
     *
     * @param  \App\Models\Stack  $stack
     * @return void
     */
    public function deleted(Stack $stack)
    {
        //
    }

    /**
     * Handle the Stack "restored" event.
     *
     * @param  \App\Models\Stack  $stack
     * @return void
     */
    public function restored(Stack $stack)
    {
        //
    }

    /**
     * Handle the Stack "force deleted" event.
     *
     * @param  \App\Models\Stack  $stack
     * @return void
     */
    public function forceDeleted(Stack $stack)
    {
        //
    }
}

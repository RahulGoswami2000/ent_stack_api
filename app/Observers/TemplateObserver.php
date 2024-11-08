<?php

namespace App\Observers;

use App\Models\Template;

class TemplateObserver
{
    /**
     * Handle the Template "created" event.
     *
     * @param  \App\Models\Template  $template
     * @return void
     */
    public function creating(Template $template)
    {
        $template->created_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $template->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }
    /**
     * Handle the Template "created" event.
     *
     * @param  \App\Models\Template  $template
     * @return void
     */
    public function created(Template $template)
    {

        $template->created_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : null;
        $template->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : null;
    }

    /**
     * Handle the Template "updated" event.
     *
     * @param  \App\Models\Template  $template
     * @return void
     */
    public function updating(Template $template)
    {
        $template->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }
    /**
     * Handle the Template "updated" event.
     *
     * @param  \App\Models\Template  $template
     * @return void
     */
    public function updated(Template $template)
    {
        if (!empty(\Auth::user()) && \Auth::user()->id) {
            $template->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : null;
        }
    }

    /**
     * Handle the Template "deleted" event.
     *
     * @param  \App\Models\Template  $template
     * @return void
     */
    public function deleting(Template $template)
    {
        // echo Auth::user()->id; die();
        $template->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $template->save();
        // print_r($subscription); die();
    }
    /**
     * Handle the Template "deleted" event.
     *
     * @param  \App\Models\Template  $template
     * @return void
     */
    public function deleted(Template $template)
    {
        //
    }

    /**
     * Handle the Template "restored" event.
     *
     * @param  \App\Models\Template  $template
     * @return void
     */
    public function restored(Template $template)
    {
        //
    }

    /**
     * Handle the Template "force deleted" event.
     *
     * @param  \App\Models\Template  $template
     * @return void
     */
    public function forceDeleted(Template $template)
    {
        //
    }
}

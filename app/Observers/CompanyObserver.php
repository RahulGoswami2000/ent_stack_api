<?php

namespace App\Observers;

use App\Models\Company;

class CompanyObserver
{
    /**
     * Handle the Company "created" event.
     *
     * @param  \App\Models\Company  $company
     * @return void
     */
    public function creating(Company $company)
    {
        $company->created_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $company->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }
    /**
     * Handle the Company "created" event.
     *
     * @param  \App\Models\Company  $company
     * @return void
     */
    public function created(Company $company)
    {
        $company->created_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : null;
        $company->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : null;
    }

    /**
     * Handle the Company "updated" event.
     *
     * @param  \App\Models\Company  $company
     * @return void
     */
    public function updating(Company $company)
    {
        $company->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }
    /**
     * Handle the Company "updated" event.
     *
     * @param  \App\Models\Company  $company
     * @return void
     */
    public function updated(Company $company)
    {
        if (!empty(\Auth::user()) && \Auth::user()->id) {
            $company->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : null;
        }
    }

    /**
     * Handle the Company "deleted" event.
     *
     * @param  \App\Models\Company  $company
     * @return void
     */
    public function deleting(Company $company)
    {
        // echo Auth::user()->id; die();
        $company->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $company->save();
    }
    /**
     * Handle the Company "deleted" event.
     *
     * @param  \App\Models\Company  $company
     * @return void
     */
    public function deleted(Company $company)
    {
        //
    }

    /**
     * Handle the Company "restored" event.
     *
     * @param  \App\Models\Company  $company
     * @return void
     */
    public function restored(Company $company)
    {
        //
    }

    /**
     * Handle the Company "force deleted" event.
     *
     * @param  \App\Models\Company  $company
     * @return void
     */
    public function forceDeleted(Company $company)
    {
        //
    }
}

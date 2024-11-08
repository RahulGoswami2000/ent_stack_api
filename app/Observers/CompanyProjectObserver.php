<?php

namespace App\Observers;

use App\Models\CompanyProject;

class CompanyProjectObserver
{
    /**
     * Handle the CompanyProject "created" event.
     *
     * @param  \App\Models\CompanyProject  $companyProject
     * @return void
     */
    public function creating(CompanyProject $companyProject)
    {
        $companyProject->created_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $companyProject->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }
    /**
     * Handle the CompanyProject "created" event.
     *
     * @param  \App\Models\CompanyProject  $companyProject
     * @return void
     */
    public function created(CompanyProject $companyProject)
    {
        //
    }
    /**
     * Handle the CompanyProject "updated" event.
     *
     * @param  \App\Models\CompanyProject  $companyProject
     * @return void
     */
    public function updating(CompanyProject $companyProject)
    {
        $companyProject->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }
    /**
     * Handle the CompanyProject "updated" event.
     *
     * @param  \App\Models\CompanyProject  $companyProject
     * @return void
     */
    public function updated(CompanyProject $companyProject)
    {
        //
    }

    /**
     * Handle the CompanyProject "deleted" event.
     *
     * @param  \App\Models\CompanyProject  $companyProject
     * @return void
     */
    public function deleting(CompanyProject $companyProject)
    {
        $companyProject->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $companyProject->save();
    }
    /**
     * Handle the CompanyProject "deleted" event.
     *
     * @param  \App\Models\CompanyProject  $companyProject
     * @return void
     */
    public function deleted(CompanyProject $companyProject)
    {
        //
    }

    /**
     * Handle the CompanyProject "restored" event.
     *
     * @param  \App\Models\CompanyProject  $companyProject
     * @return void
     */
    public function restored(CompanyProject $companyProject)
    {
        //
    }

    /**
     * Handle the CompanyProject "force deleted" event.
     *
     * @param  \App\Models\CompanyProject  $companyProject
     * @return void
     */
    public function forceDeleted(CompanyProject $companyProject)
    {
        //
    }
}

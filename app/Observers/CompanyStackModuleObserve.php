<?php

namespace App\Observers;

use App\Models\CompanyStackModules;

class CompanyStackModuleObserve
{

    /**
     * Handle the CompanyStackModules "created" event.
     *
     * @param  \App\Models\CompanyStackModules  $companyStackModule
     * @return void
     */
    public function creating(CompanyStackModules $companyStackModule)
    {
        $companyStackModule->created_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $companyStackModule->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }

    /**
     * Handle the CompanyStackModules "created" event.
     *
     * @param  \App\Models\CompanyStackModules  $companyStackModule
     * @return void
     */
    public function created(CompanyStackModules $companyStackModule)
    {
        //
    }

    /**
     * Handle the CompanyStackModules "updated" event.
     *
     * @param  \App\Models\CompanyStackModules  $companyStackModule
     * @return void
     */
    public function updating(CompanyStackModules $companyStackModule)
    {
        $companyStackModule->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }

    /**
     * Handle the CompanyStackModules "updated" event.
     *
     * @param  \App\Models\CompanyStackModules  $companyStackModule
     * @return void
     */
    public function updated(CompanyStackModules $companyStackModule)
    {
        //
    }

    /**
     * Handle the CompanyStackModules "deleted" event.
     *
     * @param  \App\Models\CompanyStackModules  $companyStackModule
     * @return void
     */
    public function deleting(CompanyStackModules $companyStackModule)
    {
        $companyStackModule->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $companyStackModule->save();
    }

    /**
     * Handle the CompanyStackModules "deleted" event.
     *
     * @param  \App\Models\CompanyStackModules  $companyStackModule
     * @return void
     */
    public function deleted(CompanyStackModules $companyStackModule)
    {
        $companyStackModule->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }

    /**
     * Handle the CompanyStackModules "restored" event.
     *
     * @param  \App\Models\CompanyStackModules  $companyStackModule
     * @return void
     */
    public function restored(CompanyStackModules $companyStackModule)
    {
        //
    }

    /**
     * Handle the CompanyStackModules "force deleted" event.
     *
     * @param  \App\Models\CompanyStackModules  $companyStackModule
     * @return void
     */
    public function forceDeleted(CompanyStackModules $companyStackModule)
    {
        //
    }
}

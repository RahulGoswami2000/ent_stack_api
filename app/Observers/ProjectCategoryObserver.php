<?php

namespace App\Observers;

use App\Models\CompanyStackCategory;

class ProjectCategoryObserver
{

    /**
     * Handle the ProjectCategory "created" event.
     *
     * @param  \App\Models\CompanyStackCategory  $projectCategory
     * @return void
     */
    public function creating(CompanyStackCategory $projectCategory)
    {
        $projectCategory->created_by = (!empty(\Auth::user()) && \Auth::user()->id)? \Auth::user()->id: NULL;
        $projectCategory->updated_by = (!empty(\Auth::user()) && \Auth::user()->id)? \Auth::user()->id: NULL;
    }

    /**
     * Handle the ProjectCategory "created" event.
     *
     * @param  \App\Models\CompanyStackCategory  $projectCategory
     * @return void
     */
    public function created(CompanyStackCategory $projectCategory)
    {
        //
    }

    /**
     * Handle the ProjectCategory "updated" event.
     *
     * @param  \App\Models\CompanyStackCategory  $projectCategory
     * @return void
     */
    public function updating(CompanyStackCategory $projectCategory)
    {
        $projectCategory->updated_by = (!empty(\Auth::user()) && \Auth::user()->id)? \Auth::user()->id: NULL;
    }

    /**
     * Handle the ProjectCategory "updated" event.
     *
     * @param  \App\Models\CompanyStackCategory  $projectCategory
     * @return void
     */
    public function updated(CompanyStackCategory $projectCategory)
    {
        //
    }

    /**
     * Handle the ProjectCategory "deleted" event.
     *
     * @param  \App\Models\CompanyStackCategory  $projectCategory
     * @return void
     */
    public function deleting(CompanyStackCategory $projectCategory)
    {
        $projectCategory->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id)? \Auth::user()->id: NULL;
        $projectCategory->save();
    }

    /**
     * Handle the ProjectCategory "deleted" event.
     *
     * @param  \App\Models\CompanyStackCategory  $projectCategory
     * @return void
     */
    public function deleted(CompanyStackCategory $projectCategory)
    {
        $projectCategory->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id)? \Auth::user()->id: NULL;
    }

    /**
     * Handle the ProjectCategory "restored" event.
     *
     * @param  \App\Models\CompanyStackCategory  $projectCategory
     * @return void
     */
    public function restored(CompanyStackCategory $projectCategory)
    {
        //
    }

    /**
     * Handle the ProjectCategory "force deleted" event.
     *
     * @param  \App\Models\CompanyStackCategory  $projectCategory
     * @return void
     */
    public function forceDeleted(CompanyStackCategory $projectCategory)
    {
        //
    }
}

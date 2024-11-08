<?php

namespace App\Observers;

use App\Models\CompanyMatrix;

class CompanyMatrixObserver
{
    /**
     * Handle the CompanyMatrix "created" event.
     *
     * @param  \App\Models\CompanyMatrix  $companyMatrix
     * @return void
     */
    public function creating(CompanyMatrix $companyMatrix)
    {
        $companyMatrix->created_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $companyMatrix->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }
    /**
     * Handle the CompanyMatrix "created" event.
     *
     * @param  \App\Models\CompanyMatrix  $companyMatrix
     * @return void
     */
    public function created(CompanyMatrix $companyMatrix)
    {
        $companyMatrix->created_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : null;
        $companyMatrix->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : null;
    }

    /**
     * Handle the CompanyMatrix "updated" event.
     *
     * @param  \App\Models\CompanyMatrix  $companyMatrix
     * @return void
     */
    public function updating(CompanyMatrix $companyMatrix)
    {
        $companyMatrix->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }
    /**
     * Handle the CompanyMatrix "updated" event.
     *
     * @param  \App\Models\CompanyMatrix  $companyMatrix
     * @return void
     */
    public function updated(CompanyMatrix $companyMatrix)
    {
        if (!empty(\Auth::user()) && \Auth::user()->id) {
            $companyMatrix->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : null;
        }
    }

    /**
     * Handle the CompanyMatrix "deleted" event.
     *
     * @param  \App\Models\CompanyMatrix  $companyMatrix
     * @return void
     */
    public function deleting(CompanyMatrix $companyMatrix)
    {
        // echo Auth::user()->id; die();
        $companyMatrix->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $companyMatrix->save();
    }
    /**
     * Handle the CompanyMatrix "deleted" event.
     *
     * @param  \App\Models\CompanyMatrix  $companyMatrix
     * @return void
     */
    public function deleted(CompanyMatrix $companyMatrix)
    {
        //
    }

    /**
     * Handle the CompanyMatrix "restored" event.
     *
     * @param  \App\Models\CompanyMatrix  $companyMatrix
     * @return void
     */
    public function restored(CompanyMatrix $companyMatrix)
    {
        //
    }

    /**
     * Handle the CompanyMatrix "force deleted" event.
     *
     * @param  \App\Models\CompanyMatrix  $companyMatrix
     * @return void
     */
    public function forceDeleted(CompanyMatrix $companyMatrix)
    {
        //
    }
}

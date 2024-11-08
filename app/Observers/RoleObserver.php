<?php

namespace App\Observers;

use App\Models\Role;

class RoleObserver
{
    /**
     * Handle the Role "created" event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function creating(Role $role)
    {
        $role->created_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $role->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }
    /**
     * Handle the Role "created" event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function created(Role $role)
    {
        $role->created_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : null;
        $role->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : null;
    }

    /**
     * Handle the Role "updated" event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function updating(Role $role)
    {
        $role->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
    }
    /**
     * Handle the Role "updated" event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function updated(Role $role)
    {
        if (!empty(\Auth::user()) && \Auth::user()->id) {
            $role->updated_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : null;
        }
    }

    /**
     * Handle the Role "deleted" event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function deleting(Role $role)
    {
        // echo Auth::user()->id; die();
        $role->deleted_by = (!empty(\Auth::user()) && \Auth::user()->id) ? \Auth::user()->id : NULL;
        $role->save();
        // print_r($subscription); die();
    }
    /**
     * Handle the Role "deleted" event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function deleted(Role $role)
    {
        //
    }

    /**
     * Handle the Role "restored" event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function restored(Role $role)
    {
        //
    }

    /**
     * Handle the Role "force deleted" event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function forceDeleted(Role $role)
    {
        //
    }
}

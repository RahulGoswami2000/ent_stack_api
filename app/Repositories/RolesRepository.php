<?php

namespace App\Repositories;

use App\Library\FunctionUtils;
use App\Models\Role;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Request;

class RolesRepository extends BaseRepository
{

    private $role;

    public function __construct()
    {
        $this->role = new Role();
    }

    /**
     * List Roles
     */
    public function list()
    {
        $query = \DB::table('mst_roles')
            ->select(
                'mst_roles.id',
                'mst_roles.name',
                'mst_roles.role_type',
                'mst_roles.privileges',
                'mst_roles.is_editable',
                \DB::raw('IF(`mst_roles`.`is_active` = 1,"' .  __('labels.active')  . '","' .  __('labels.inactive')  . '") AS display_status')
            )
            ->whereNull('mst_roles.deleted_at');
        $data  = $query->get()->toArray();
        $count = $query->count();
        return ['data' => $data, 'count' => $count];
    }

    /**
     * Store Roles
     */
    public function store($request)
    {
        return Role::create([
            'name'       => $request->roles_name,
            'role_type'  => $request->role_type,
            'privileges' => $request->privileges,
        ]);
    }

    /**
     * Details Roles
     */
    public function details($id)
    {
        $dataDetails = $this->role->find($id);

        if (empty($dataDetails)) {
            return null;
        }

        return $dataDetails;
    }

    /**
     * Update Roles
     */
    public function update($id, $request)
    {
        $data = $this->role->find($id);
        $data->update([
            'name'       => $request->roles_name,
            'role_type'  => $request->role_type,
            'privileges' => $request->privileges,
        ]);
        return $data;
    }

    /**
     * Delete Roles
     */
    public function destroy($id)
    {
        return $this->role->find($id)->delete();
    }

    /**
     * Roles Status Change
     */
    public function changeStatus($id, $request)
    {
        $data = $this->role->find($id);
        $data->update([
            'is_active' => $request->is_active,
        ]);

        return $data;
    }
}

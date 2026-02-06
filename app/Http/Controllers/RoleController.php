<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::paginate(100);
        $permissions = $this->sortByModules();
        return view('admin.role.index', compact('roles', 'permissions'));
    }

    public function create()
    {
        $permissions = $this->sortByModules();
        return view('admin.role.form', compact('permissions'));
    }

    public function insert(RoleRequest $request)
    {
        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);
    
        // Convert permission IDs to names
        $permissions = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
    
        // Sync permissions using names
        $role->syncPermissions($permissions);
    
        toastr()->success('Role Created Successfully!');
        return redirect()->route('roles');
    }

    public function show(Role $role)
    {
        return view('admin.role.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $permissions = $this->sortByModules();
        return view('admin.role.form', compact('role', 'permissions'));
    }

    public function update(Role $role, RoleRequest $request)
    {
        $role->name = $request->name;
        $role->save();
        $role->syncPermissions(array_map('intval', $request->permissions));
        toastr()->success('Role Edited Successfully!');
        return redirect()->route('roles');
    }

    public function delete(Role $role)
    {
        if ($role->name == "super-admin" || $role->name == "admin" || $role->name == "customer")
            return redirect()->route('roles')->withError('This role cannot be deleted!');

        if (auth()->user()->isAdmin() & !auth()->user()->can('delete-admin'))
            return redirect()->route('roles')->withError('Role cannot be deleted!');
        $role->delete();
        toastr()->success('Role Deleted Successfully!');
        return redirect()->route('roles');
    }

    private static function sortByModules()
    {
        foreach (Permission::get()->pluck('name') as $index => $permission) {
            $exploded[$index]['exploded'] = explode('-', $permission, 2);
            $exploded[$index]['name'] = $permission;
        }
        foreach ($exploded as $index => $ex) {
            $final[$ex['exploded'][1] ?? $ex['exploded'][0]][] = Permission::where('name', $ex['name'])->first();
        }
        return $final;
    }
}

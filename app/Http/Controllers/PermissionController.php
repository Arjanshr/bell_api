<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::paginate(100);
        return view('admin.permission.index', compact('permissions'));
    }

    public function create()
    {
        return view('admin.permission.form');
    }

    public function insert(PermissionRequest $request)
    {
        if ($request->fields) {
            foreach ($request->fields as $field) {
                Permission::create([
                    'name' => $field . '-' . $request->name,
                    'guard_name' => 'web'
                ]);
            }
        } else {
            $permission = Permission::create([
                'name' =>  $request->name,
                'guard_name' => 'web'
            ]);
        }
        toastr()->success('Permission Created Successfully!');
        return redirect()->route('permissions');
    }

    public function show(Permission $permission)
    {
        return view('admin.permission.show', compact('permission'));
    }

    public function edit(Permission $permission)
    {
        return view('admin.permission.form', compact('permission'));
    }

    public function update(Permission $permission, PermissionRequest $request)
    {
        $permission->name = $request->name;
        $permission->save();
        toastr()->success('Permission Edited Successfully!');
        return redirect()->route('permissions');
    }

    public function delete(Permission $permission)
    {
        $permission->delete();
        toastr()->success('Permission Deleted Successfully!');
        return redirect()->route('permissions');
    }
}

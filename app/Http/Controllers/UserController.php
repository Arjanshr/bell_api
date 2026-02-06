<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::get();
        return view('admin.user.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all()->pluck('name');
        $brands = Brand::all();
        return view('admin.user.form', compact('roles', 'brands'));
    }

    public function insert(UserRequest $request)
    {
        $data = $request->all();
        $data['password'] = bcrypt('password');

        if (in_array('vendor', $request->role)) {
            $data['brand_id'] = $request->brand_id;
        } else {
            $data['brand_id'] = null;
        }

        $user = User::create($data);

        $roles = $this->checkRoles($request->role);
        $user->assignRole($roles);

        if (in_array('vendor', $request->role)) {
            toastr()->success('Vendor Created Successfully!');
            return redirect()->route('vendors.index');
        }

        toastr()->success('User Created Successfully!');
        return redirect()->route('users');
    }

    public function show(User $user)
    {
        return view('admin.user.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all()->pluck('name');
        $brands = Brand::all();
        return view('admin.user.form', compact('roles', 'brands', 'user'));
    }

    public function update(User $user, UserRequest $request)
    {
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->dob = $request->dob;
        $user->gender = $request->gender;
        $user->address = $request->address;
        $user->email_verified_at = now();

        if (auth()->user()->can('change-user-password') && $this->passwordValidation($request->password)) {
            $user->password = bcrypt($request->password);
        }

        if (in_array('vendor', $request->role)) {
            $user->brand_id = $request->brand_id;
        } else {
            $user->brand_id = null;
        }

        $user->save();

        $roles = $this->checkRoles($request->role);
        $user->syncRoles($roles);

        toastr()->success('User Edited Successfully!');
        return redirect()->route('users');
    }

    public function delete(User $user)
    {
        if ($user->isAdmin() && !auth()->user()->can('delete-admin')) {
            return redirect()->route('users')->withError('User cannot be deleted!');
        }

        $user->delete();

        toastr()->success('User Deleted Successfully!');
        return redirect()->route('users');
    }

    private function checkRoles(array $roles): array
    {
        $roles = array_filter($roles, function ($role) {
            if ($role === 'super-admin') {
                return false;
            }

            if ($role === 'admin' && !auth()->user()->can('add-admin')) {
                return false;
            }

            return true;
        });

        return array_values($roles);
    }

    public function activities(User $user)
    {
        $activities = Activity::where('causer_id', $user->id)->orderBy('id', 'DESC')->paginate(100);
        return view('admin.user.activities', compact('activities'));
    }

    public function showActivity(Activity $activity)
    {
        return view('admin.user.show_activity', compact('activity'));
    }

    private function passwordValidation($password)
    {
        return strlen($password) >= 6;
    }

    public function vendors()
    {
        $vendors = User::role('vendor')->with('brand')->get();
        return view('admin.user.vendors', compact('vendors'));
    }

    public function export(Request $request)
    {
        $users = User::with('roles')->orderBy('name')->get();

        $csv_data = [];
        $csv_data[] = [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Address',
        ];

        foreach ($users as $user) {
            $roles = $user->roles->pluck('name')->implode(', ');
            $csv_data[] = [
                $user->id,
                $user->name,
                $user->email,
                $user->phone,
                $user->address,
            ];
        }

        $filename = 'users_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $handle = fopen('php://temp', 'w+');

        foreach ($csv_data as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);

        return Response::stream(function () use ($handle) {
            fpassthru($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    public function deactivate(User $user)
    {
        if ($user->hasRole('super-admin')) {
            return redirect()->back()->withError('Superadmins cannot be deactivated.');
        }
        if ($user->isAdmin() && !auth()->user()->can('edit-admin')) {
            return redirect()->back()->withError('You do not have permission to deactivate this user.');
        }

        $user->status = 'inactive';
        $user->save();

        $message = $user->hasRole('vendor') ? 'Vendor deactivated successfully.' : 'User deactivated successfully.';
        toastr()->success($message);
        return redirect()->back();
    }

    public function activate(User $user)
    {
        if ($user->hasRole('super-admin')) {
            return redirect()->back()->withError('Superadmins cannot be activated.');
        }
        if ($user->isAdmin() && !auth()->user()->can('edit-admin')) {
            return redirect()->back()->withError('You do not have permission to activate this user.');
        }

        $user->status = 'active';
        $user->save();

        $message = $user->hasRole('vendor') ? 'Vendor activated successfully.' : 'User activated successfully.';
        toastr()->success($message);
        return redirect()->back();
    }
}

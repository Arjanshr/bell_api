<?php

namespace Database\Seeders;

use App\Services\GeneralService;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    public function __construct(private GeneralService $general)
    {
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'browse-activities']);
        Permission::create(['name' => 'read-activities']);
        Permission::create(['name' => 'add-admin']);
        Permission::create(['name' => 'read-admin']);
        Permission::create(['name' => 'edit-admin']);
        Permission::create(['name' => 'delete-admin']);
        
        Permission::create(['name' => 'browse-general-settings']);
        Permission::create(['name' => 'edit-general-settings']);

        Permission::create(['name' => 'browse-contents']);
        Permission::create(['name' => 'edit-contents']);
        Permission::create(['name' => 'delete-contents']);

        Permission::create(['name' => 'browse-roles']);
        Permission::create(['name' => 'read-roles']);
        Permission::create(['name' => 'edit-roles']);
        Permission::create(['name' => 'add-roles']);
        Permission::create(['name' => 'delete-roles']);

        Permission::create(['name' => 'browse-permissions']);
        Permission::create(['name' => 'read-permissions']);
        Permission::create(['name' => 'edit-permissions']);
        Permission::create(['name' => 'add-permissions']);
        Permission::create(['name' => 'delete-permissions']);
        
        $models = $this->general->getModels(app_path() . "/Models");
        foreach ($models as $model) {
            $formatted = $this->general->convertCamelCase($model);
            $name = $this->general->pluralize(2, $formatted);
            Permission::create(['name' => 'browse-' . $name]);
            Permission::create(['name' => 'read-' . $name]);
            Permission::create(['name' => 'edit-' . $name]);
            Permission::create(['name' => 'add-' . $name]);
            Permission::create(['name' => 'delete-' . $name]);
        }


        Role::create(['name' => 'super-admin']);
        $adminRole = Role::create(['name' => 'admin']);
        Role::create(['name' => 'customer']);
        $managerRole = Role::create(['name' => 'manager']);
        $employeeRole = Role::create(['name' => 'employee']);

        $managerRole->givePermissionTo([
            'browse-users',
            'read-users',
            'add-users',
            'edit-users',
            'delete-users',
        ]);
        
        $adminRole->givePermissionTo([
            'browse-users',
            'read-users',
            'add-users',
            'edit-users',
            'delete-users',
        ]);

        $employeeRole->givePermissionTo([
            'browse-users',
            'read-users',
            'add-users',
            'edit-users',
            'delete-users',
        ]);
    }
}

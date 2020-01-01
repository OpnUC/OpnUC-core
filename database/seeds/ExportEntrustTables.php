<?php

use Illuminate\Database\Seeder;

class ExportEntrustTables extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Permission
        $perms = \App\Permission::all();

        echo "###Permission\n";
        foreach ($perms as $perm) {
            echo "{$perm['id']}\t{$perm['name']}\t{$perm['display_name']}\t{$perm['description']}\t{$perm['created_at']}\t{$perm['updated_at']}\n";
        }

        // Role
        $roles = \App\Role::all();

        $role_perms = [];
        $role_users = [];
        echo "###Role\n";
        foreach ($roles as $role) {
            echo "{$role['id']}\t{$role['name']}\t{$role['display_name']}\t{$role['description']}\t{$role['created_at']}\t{$role['updated_at']}\n";

            $role_in_perms = $role->perms()->get();
            foreach ($role_in_perms as $role_in_perm) {
                $role_perms[$role['id']][] = $role_in_perm['id'];
            }

            $role_in_users = $role->users()->get();
            foreach ($role_in_users as $role_in_user) {
                $role_users[$role_in_user['id']][] = $role['id'];
            }
        }

        // Role Permission
        echo "###RolePermission\n";
        foreach ($role_perms as $role_id => $value) {
            foreach ($value as $perm_id) {
                echo "{$role_id}\t{$perm_id}\n";
            }
        }

        // User Role
        echo "###UserRole\n";
        foreach ($role_users as $user_id => $value) {
            foreach ($value as $role_id) {
                echo "{$user_id}\t{$role_id}\n";
            }
        }

    }
}

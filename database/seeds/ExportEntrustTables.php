<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
        $perms = DB::table('permissions')->get()->toArray();

        echo "###Permission\n";
        foreach ($perms as $perm) {
            echo "{$perm->id}\t{$perm->name}\t{$perm->display_name}\t{$perm->description}\t{$perm->created_at}\t{$perm->updated_at}\n";
        }

        // Role
        $roles = DB::table('roles')->get()->toArray();

        $role_perms = [];
        $role_users = [];
        echo "###Role\n";
        foreach ($roles as $role) {
            echo "{$role->id}\t{$role->name}\t{$role->display_name}\t{$role->description}\t{$role->created_at}\t{$role->updated_at}\n";

            if($role){
                $role_in_perms = DB::table('permission_role')->where('role_id', $role->id)->get()->toArray();
                foreach ($role_in_perms as $role_in_perm) {
                    $role_perms[$role->id][] = $role_in_perm->permission_id;
                }
            }

            $role_in_users = DB::table('role_user')->where('role_id', $role->id)->get()->toArray();
            foreach ($role_in_users as $role_in_user) {
                $role_users[$role_in_user->user_id][] = $role->id;
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

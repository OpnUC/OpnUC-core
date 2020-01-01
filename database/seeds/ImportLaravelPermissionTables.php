<?php

use Illuminate\Database\Seeder;

class ImportLaravelPermissionTables extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lines = array();
        while ($line = fgets(STDIN)) {
            $lines[] = trim($line);
        }

        $table = null;
        foreach ($lines as $line) {
            switch ($line) {
                case '###Permission':
                    $table = \App\Permission::class;
                    break;
                case '###Role':
                    $table = \App\Role::class;
                    break;
                case '###RolePermission':
                    $table = 'RolePermission';
                    break;
                case '###UserRole':
                    $table = 'UserRole';
                    break;
                default:
                    if ($table == null) {
                        continue;
                    }
                    $vals = preg_split("/\t/", $line);
                    if (ends_with($table, \App\Permission::class) || ends_with($table, \App\Role::class)) {
                        echo "Insert {$table} {$line}\n";
                        $item = new $table;
                        $item['id'] = $vals[0];
                        $item['name'] = $vals[1];
                        $item['display_name'] = $vals[2];
                        $item['description'] = $vals[3];
                        $item['created_at'] = $vals[4];
                        $item['updated_at'] = $vals[5];
                        $item->save();
                    } elseif ($table == 'RolePermission') {
                        echo "Role {$vals[0]} Attache Perm {$vals[1]}\n";
                        $role = \App\Role::find($vals[0]);
                        $perm = \App\Permission::find($vals[1]);
                        $role->givePermissionTo($perm);
                    } elseif ($table == 'UserRole') {
                        echo "User {$vals[0]} Attache Role {$vals[1]}\n";
                        $user = \App\User::find($vals[0]);
                        $role = \App\Role::find($vals[1]);
                        $user->assignRole($role);
                    }
                    break;
            }
        }
    }
}

<?php

$role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'read_only', 'guard_name' => 'web']);
$permissions = \Spatie\Permission\Models\Permission::where('name', 'like', 'ViewAny:%')->orWhere('name', 'like', 'View:%')->get();
$role->syncPermissions($permissions);
echo "Role created and permissions assigned.\n";

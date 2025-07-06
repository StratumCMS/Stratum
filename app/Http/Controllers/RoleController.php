<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(){
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        return view('admin.roles', compact('roles', 'permissions'));
    }

    public function store(Request $request){
        $request->validate([
            'name'        => 'required|string|max:50|unique:roles',
            'description' => 'nullable|string|max:255',
            'color'       => 'nullable|string|max:20',
            'icon'        => 'nullable|string|max:50',
        ]);

        $role = Role::create($request->only(['name', 'description', 'color', 'icon']));

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        log_activity('roles', 'Création', "Rôle « {$role->name} » créé");

        return back()->with('success', 'Rôle créé avec succès.');
    }

    public function update(Request $request, Role $role){
        $request->validate([
            'name'        => 'required|string|max:50|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:255',
            'color'       => 'nullable|string|max:20',
            'icon'        => 'nullable|string|max:50',
        ]);

        $role->update($request->only(['name', 'description', 'color', 'icon']));

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        log_activity('roles', 'Mise à jour', "Rôle « {$role->name} » mis à jour");

        return back()->with('success', 'Rôle mis à jour.');
    }

    public function destroy(Role $role){
        log_activity('roles', 'Suppression', "Rôle « {$role->name} » supprimé");

        $role->permissions()->detach();
        $role->users()->detach();
        $role->delete();

        return back()->with('success', 'Rôle supprimé.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departments = Department::with('manager')->orderBy('name')->paginate(10);
        return view('departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $managers = User::whereHas('role', function($query) {
            $query->where('name', 'Manager');
        })->orderBy('name')->get();
        
        return view('departments.create', compact('managers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments',
            'location' => 'required|string|max:255',
            'manager_id' => 'required|exists:users,id',
        ]);

        $department = Department::create($validated);

        return redirect()->route('departments.index')
            ->with('success', 'Departamento creado correctamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $department = Department::with(['manager', 'assets', 'users'])->findOrFail($id);
        return view('departments.show', compact('department'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $department = Department::findOrFail($id);
        $managers = User::whereHas('role', function($query) {
            $query->where('name', 'Manager');
        })->orderBy('name')->get();
        
        return view('departments.edit', compact('department', 'managers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code,' . $id,
            'location' => 'required|string|max:255',
            'manager_id' => 'required|exists:users,id',
        ]);

        $department->update($validated);

        return redirect()->route('departments.show', $department->id)
            ->with('success', 'Departamento actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        
        // Verificar si hay activos o usuarios asignados a este departamento
        if ($department->assets()->count() > 0 || $department->users()->count() > 0) {
            return redirect()->route('departments.index')
                ->with('error', 'No se puede eliminar el departamento porque tiene activos o usuarios asignados');
        }
        
        $department->delete();

        return redirect()->route('departments.index')
            ->with('success', 'Departamento eliminado correctamente');
    }
}
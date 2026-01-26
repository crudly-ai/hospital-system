<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Department;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Yajra\DataTables\Facades\DataTables;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->can('view_doctor')) {
            abort(403, 'You do not have permission to view doctors.');
        }
        
        if (($request->wantsJson() || $request->ajax()) && !$request->header('X-Inertia')) {
            $doctors = Doctor::select('doctors.*')->with(['department']);
            
            // Apply ownership filter
            if (auth()->user()->can('manage_all_doctor')) {
                // User can see all doctors
            } elseif (auth()->user()->can('manage_own_doctor')) {
                $doctors->where('created_by', auth()->id());
            }
            
            // Apply department_id filter
            if ($request->filled('department_id_filter')) {
                $doctors->where('department_id', $request->department_id_filter);
            }
            
            // Apply status filter
            if ($request->filled('status_filter')) {
                $doctors->where('status', $request->status_filter);
            }
            
            // Apply date range filter
            if ($request->filled('date_from')) {
                $doctors->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $doctors->whereDate('created_at', '<=', $request->date_to);
            }

            
            return DataTables::of($doctors)
                ->addColumn('created_at_formatted', function ($doctor) {
                    return $doctor->created_at->format('Y-m-d H:i:s');
                })
                ->addColumn('department_name', function ($doctor) {
                    return $doctor->department ? $doctor->department->name : null;
                })

                ->filterColumn('status', function($query, $keyword) {
                    $query->where('status', 'like', "%{$keyword}%");
                })

                ->escapeColumns([])
                ->make(true);
        }

        $statusOptions = [
            ['value' => 'active', 'label' => 'Active'],
            ['value' => 'inactive', 'label' => 'Inactive'],
        ];



        return Inertia::render('doctors/index', [
            'statusOptions' => $statusOptions,

            'departments' => Department::all(),

            'currencySettings' => [
                'currency_symbol' => \App\Models\SystemSetting::get('currency_symbol', '$'),
                'currency_position' => \App\Models\SystemSetting::get('currency_position', 'before'),
                'decimal_separator' => \App\Models\SystemSetting::get('decimal_separator', '.'),
                'thousand_separator' => \App\Models\SystemSetting::get('thousand_separator', ','),
            ],
        ]);
    }

    public function show(Doctor $doctor)
    {
        if (!auth()->user()->can('view_doctor')) {
            abort(403, 'You do not have permission to view this doctor.');
        }

        $doctor->load(['department']);
        return response()->json($doctor);
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('create_doctor')) {
            abort(403, 'You do not have permission to create doctors.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'specialty' => 'required|string',
            'department_id' => 'nullable|integer',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'status' => 'required',

        ]);

        Doctor::create(array_merge($request->all(), ['created_by' => auth()->id()]));

        return redirect()->back()->with('success', 'Doctor created successfully.');
    }

    public function update(Request $request, Doctor $doctor)
    {
        if (!auth()->user()->can('edit_doctor')) {
            abort(403, 'You do not have permission to edit doctors.');
        }
        
        // Check ownership for manage_own permission
        if (!auth()->user()->can('manage_all_doctor') && 
            auth()->user()->can('manage_own_doctor') && 
            $doctor->created_by !== auth()->id()) {
            abort(403, 'You can only edit doctors you created.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'specialty' => 'required|string',
            'department_id' => 'nullable|integer',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'status' => 'required',

        ]);

        $doctor->update($request->all());

        return redirect()->back()->with('success', 'Doctor updated successfully.');
    }

    public function destroy(Doctor $doctor)
    {
        if (!auth()->user()->can('delete_doctor')) {
            abort(403, 'You do not have permission to delete doctors.');
        }
        
        // Check ownership for manage_own permission
        if (!auth()->user()->can('manage_all_doctor') && 
            auth()->user()->can('manage_own_doctor') && 
            $doctor->created_by !== auth()->id()) {
            abort(403, 'You can only delete doctors you created.');
        }
        
        $doctor->delete();

        return redirect()->back()->with('success', 'Doctor deleted successfully.');
    }

}
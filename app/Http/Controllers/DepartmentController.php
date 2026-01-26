<?php

namespace App\Http\Controllers;

use App\Models\Department;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Yajra\DataTables\Facades\DataTables;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->can('view_department')) {
            abort(403, 'You do not have permission to view departments.');
        }
        
        if (($request->wantsJson() || $request->ajax()) && !$request->header('X-Inertia')) {
            $departments = Department::select('departments.*');
            
            // Apply ownership filter
            if (auth()->user()->can('manage_all_department')) {
                // User can see all departments
            } elseif (auth()->user()->can('manage_own_department')) {
                $departments->where('created_by', auth()->id());
            }
            
            // Apply status filter
            if ($request->filled('status_filter')) {
                $departments->where('status', $request->status_filter);
            }
            
            // Apply date range filter
            if ($request->filled('date_from')) {
                $departments->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $departments->whereDate('created_at', '<=', $request->date_to);
            }

            
            return DataTables::of($departments)
                ->addColumn('created_at_formatted', function ($department) {
                    return $department->created_at->format('Y-m-d H:i:s');
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



        return Inertia::render('departments/index', [
            'statusOptions' => $statusOptions,


            'currencySettings' => [
                'currency_symbol' => \App\Models\SystemSetting::get('currency_symbol', '$'),
                'currency_position' => \App\Models\SystemSetting::get('currency_position', 'before'),
                'decimal_separator' => \App\Models\SystemSetting::get('decimal_separator', '.'),
                'thousand_separator' => \App\Models\SystemSetting::get('thousand_separator', ','),
            ],
        ]);
    }

    public function show(Department $department)
    {
        if (!auth()->user()->can('view_department')) {
            abort(403, 'You do not have permission to view this department.');
        }

        $department->load([]);
        return response()->json($department);
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('create_department')) {
            abort(403, 'You do not have permission to create departments.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required',

        ]);

        Department::create(array_merge($request->all(), ['created_by' => auth()->id()]));

        return redirect()->back()->with('success', 'Department created successfully.');
    }

    public function update(Request $request, Department $department)
    {
        if (!auth()->user()->can('edit_department')) {
            abort(403, 'You do not have permission to edit departments.');
        }
        
        // Check ownership for manage_own permission
        if (!auth()->user()->can('manage_all_department') && 
            auth()->user()->can('manage_own_department') && 
            $department->created_by !== auth()->id()) {
            abort(403, 'You can only edit departments you created.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required',

        ]);

        $department->update($request->all());

        return redirect()->back()->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        if (!auth()->user()->can('delete_department')) {
            abort(403, 'You do not have permission to delete departments.');
        }
        
        // Check ownership for manage_own permission
        if (!auth()->user()->can('manage_all_department') && 
            auth()->user()->can('manage_own_department') && 
            $department->created_by !== auth()->id()) {
            abort(403, 'You can only delete departments you created.');
        }
        
        $department->delete();

        return redirect()->back()->with('success', 'Department deleted successfully.');
    }

}
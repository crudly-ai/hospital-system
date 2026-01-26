<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Patient;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Yajra\DataTables\Facades\DataTables;

class AdmissionController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->can('view_admission')) {
            abort(403, 'You do not have permission to view admissions.');
        }
        
        if (($request->wantsJson() || $request->ajax()) && !$request->header('X-Inertia')) {
            $admissions = Admission::select('admissions.*')->with(['patient']);
            
            // Apply ownership filter
            if (auth()->user()->can('manage_all_admission')) {
                // User can see all admissions
            } elseif (auth()->user()->can('manage_own_admission')) {
                $admissions->where('created_by', auth()->id());
            }
            
            // Apply patient_id filter
            if ($request->filled('patient_id_filter')) {
                $admissions->where('patient_id', $request->patient_id_filter);
            }
            
            // Apply admitted_on date range filter
            if ($request->filled('admitted_on_from')) {
                $admissions->whereDate('admitted_on', '>=', $request->admitted_on_from);
            }
            if ($request->filled('admitted_on_to')) {
                $admissions->whereDate('admitted_on', '<=', $request->admitted_on_to);
            }
            
            // Apply discharged_on date range filter
            if ($request->filled('discharged_on_from')) {
                $admissions->whereDate('discharged_on', '>=', $request->discharged_on_from);
            }
            if ($request->filled('discharged_on_to')) {
                $admissions->whereDate('discharged_on', '<=', $request->discharged_on_to);
            }
            
            // Apply status filter
            if ($request->filled('status_filter')) {
                $admissions->where('status', $request->status_filter);
            }
            
            // Apply date range filter
            if ($request->filled('date_from')) {
                $admissions->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $admissions->whereDate('created_at', '<=', $request->date_to);
            }

            
            return DataTables::of($admissions)
                ->addColumn('created_at_formatted', function ($admission) {
                    return $admission->created_at->format('Y-m-d H:i:s');
                })
                ->addColumn('patient_name', function ($admission) {
                    return $admission->patient ? $admission->patient->name : null;
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



        return Inertia::render('admissions/index', [
            'statusOptions' => $statusOptions,

            'patients' => Patient::all(),

            'currencySettings' => [
                'currency_symbol' => \App\Models\SystemSetting::get('currency_symbol', '$'),
                'currency_position' => \App\Models\SystemSetting::get('currency_position', 'before'),
                'decimal_separator' => \App\Models\SystemSetting::get('decimal_separator', '.'),
                'thousand_separator' => \App\Models\SystemSetting::get('thousand_separator', ','),
            ],
        ]);
    }

    public function show(Admission $admission)
    {
        if (!auth()->user()->can('view_admission')) {
            abort(403, 'You do not have permission to view this admission.');
        }

        $admission->load(['patient']);
        return response()->json($admission);
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('create_admission')) {
            abort(403, 'You do not have permission to create admissions.');
        }
        
        $request->validate([
            'patient_id' => 'nullable|integer',
            'admitted_on' => 'required|date',
            'discharged_on' => 'nullable|date',
            'room_number' => 'required|string',
            'status' => 'required',

        ]);

        Admission::create(array_merge($request->all(), ['created_by' => auth()->id()]));

        return redirect()->back()->with('success', 'Admission created successfully.');
    }

    public function update(Request $request, Admission $admission)
    {
        if (!auth()->user()->can('edit_admission')) {
            abort(403, 'You do not have permission to edit admissions.');
        }
        
        // Check ownership for manage_own permission
        if (!auth()->user()->can('manage_all_admission') && 
            auth()->user()->can('manage_own_admission') && 
            $admission->created_by !== auth()->id()) {
            abort(403, 'You can only edit admissions you created.');
        }
        
        $request->validate([
            'patient_id' => 'nullable|integer',
            'admitted_on' => 'required|date',
            'discharged_on' => 'nullable|date',
            'room_number' => 'required|string',
            'status' => 'required',

        ]);

        $admission->update($request->all());

        return redirect()->back()->with('success', 'Admission updated successfully.');
    }

    public function destroy(Admission $admission)
    {
        if (!auth()->user()->can('delete_admission')) {
            abort(403, 'You do not have permission to delete admissions.');
        }
        
        // Check ownership for manage_own permission
        if (!auth()->user()->can('manage_all_admission') && 
            auth()->user()->can('manage_own_admission') && 
            $admission->created_by !== auth()->id()) {
            abort(403, 'You can only delete admissions you created.');
        }
        
        $admission->delete();

        return redirect()->back()->with('success', 'Admission deleted successfully.');
    }

}
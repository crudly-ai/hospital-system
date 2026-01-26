<?php

namespace App\Http\Controllers;

use App\Models\Patient;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Yajra\DataTables\Facades\DataTables;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->can('view_patient')) {
            abort(403, 'You do not have permission to view patients.');
        }
        
        if (($request->wantsJson() || $request->ajax()) && !$request->header('X-Inertia')) {
            $patients = Patient::select('patients.*');
            
            // Apply ownership filter
            if (auth()->user()->can('manage_all_patient')) {
                // User can see all patients
            } elseif (auth()->user()->can('manage_own_patient')) {
                $patients->where('created_by', auth()->id());
            }
            
            // Apply dob date range filter
            if ($request->filled('dob_from')) {
                $patients->whereDate('dob', '>=', $request->dob_from);
            }
            if ($request->filled('dob_to')) {
                $patients->whereDate('dob', '<=', $request->dob_to);
            }
            
            // Apply status filter
            if ($request->filled('status_filter')) {
                $patients->where('status', $request->status_filter);
            }
            
            // Apply date range filter
            if ($request->filled('date_from')) {
                $patients->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $patients->whereDate('created_at', '<=', $request->date_to);
            }

            
            return DataTables::of($patients)
                ->addColumn('created_at_formatted', function ($patient) {
                    return $patient->created_at->format('Y-m-d H:i:s');
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



        return Inertia::render('patients/index', [
            'statusOptions' => $statusOptions,


            'currencySettings' => [
                'currency_symbol' => \App\Models\SystemSetting::get('currency_symbol', '$'),
                'currency_position' => \App\Models\SystemSetting::get('currency_position', 'before'),
                'decimal_separator' => \App\Models\SystemSetting::get('decimal_separator', '.'),
                'thousand_separator' => \App\Models\SystemSetting::get('thousand_separator', ','),
            ],
        ]);
    }

    public function show(Patient $patient)
    {
        if (!auth()->user()->can('view_patient')) {
            abort(403, 'You do not have permission to view this patient.');
        }

        $patient->load([]);
        return response()->json($patient);
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('create_patient')) {
            abort(403, 'You do not have permission to create patients.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'dob' => 'nullable|date',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'status' => 'required',

        ]);

        Patient::create(array_merge($request->all(), ['created_by' => auth()->id()]));

        return redirect()->back()->with('success', 'Patient created successfully.');
    }

    public function update(Request $request, Patient $patient)
    {
        if (!auth()->user()->can('edit_patient')) {
            abort(403, 'You do not have permission to edit patients.');
        }
        
        // Check ownership for manage_own permission
        if (!auth()->user()->can('manage_all_patient') && 
            auth()->user()->can('manage_own_patient') && 
            $patient->created_by !== auth()->id()) {
            abort(403, 'You can only edit patients you created.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'dob' => 'nullable|date',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'status' => 'required',

        ]);

        $patient->update($request->all());

        return redirect()->back()->with('success', 'Patient updated successfully.');
    }

    public function destroy(Patient $patient)
    {
        if (!auth()->user()->can('delete_patient')) {
            abort(403, 'You do not have permission to delete patients.');
        }
        
        // Check ownership for manage_own permission
        if (!auth()->user()->can('manage_all_patient') && 
            auth()->user()->can('manage_own_patient') && 
            $patient->created_by !== auth()->id()) {
            abort(403, 'You can only delete patients you created.');
        }
        
        $patient->delete();

        return redirect()->back()->with('success', 'Patient deleted successfully.');
    }

}
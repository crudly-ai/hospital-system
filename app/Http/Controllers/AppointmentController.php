<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Yajra\DataTables\Facades\DataTables;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->can('view_appointment')) {
            abort(403, 'You do not have permission to view appointments.');
        }
        
        if (($request->wantsJson() || $request->ajax()) && !$request->header('X-Inertia')) {
            $appointments = Appointment::select('appointments.*')->with(['doctor', 'patient']);
            
            // Apply ownership filter
            if (auth()->user()->can('manage_all_appointment')) {
                // User can see all appointments
            } elseif (auth()->user()->can('manage_own_appointment')) {
                $appointments->where('created_by', auth()->id());
            }
            
            // Apply doctor_id filter
            if ($request->filled('doctor_id_filter')) {
                $appointments->where('doctor_id', $request->doctor_id_filter);
            }
            
            // Apply patient_id filter
            if ($request->filled('patient_id_filter')) {
                $appointments->where('patient_id', $request->patient_id_filter);
            }
            
            // Apply date date range filter
            if ($request->filled('date_from')) {
                $appointments->whereDate('date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $appointments->whereDate('date', '<=', $request->date_to);
            }
            
            // Apply time time range filter
            if ($request->filled('time_from')) {
                $appointments->whereTime('time', '>=', $request->time_from);
            }
            if ($request->filled('time_to')) {
                $appointments->whereTime('time', '<=', $request->time_to);
            }
            
            // Apply status filter
            if ($request->filled('status_filter')) {
                $appointments->where('status', $request->status_filter);
            }
            
            // Apply date range filter
            if ($request->filled('date_from')) {
                $appointments->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $appointments->whereDate('created_at', '<=', $request->date_to);
            }

            
            return DataTables::of($appointments)
                ->addColumn('created_at_formatted', function ($appointment) {
                    return $appointment->created_at->format('Y-m-d H:i:s');
                })
                ->addColumn('doctor_name', function ($appointment) {
                    return $appointment->doctor ? $appointment->doctor->name : null;
                })
                ->addColumn('patient_name', function ($appointment) {
                    return $appointment->patient ? $appointment->patient->name : null;
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



        return Inertia::render('appointments/index', [
            'statusOptions' => $statusOptions,

            'doctors' => Doctor::all(),
            'patients' => Patient::all(),

            'currencySettings' => [
                'currency_symbol' => \App\Models\SystemSetting::get('currency_symbol', '$'),
                'currency_position' => \App\Models\SystemSetting::get('currency_position', 'before'),
                'decimal_separator' => \App\Models\SystemSetting::get('decimal_separator', '.'),
                'thousand_separator' => \App\Models\SystemSetting::get('thousand_separator', ','),
            ],
        ]);
    }

    public function show(Appointment $appointment)
    {
        if (!auth()->user()->can('view_appointment')) {
            abort(403, 'You do not have permission to view this appointment.');
        }

        $appointment->load(['doctor', 'patient']);
        return response()->json($appointment);
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('create_appointment')) {
            abort(403, 'You do not have permission to create appointments.');
        }
        
        $request->validate([
            'doctor_id' => 'nullable|integer',
            'patient_id' => 'nullable|integer',
            'date' => 'required|date',
            'time' => 'required|time',
            'status' => 'required',

        ]);

        Appointment::create(array_merge($request->all(), ['created_by' => auth()->id()]));

        return redirect()->back()->with('success', 'Appointment created successfully.');
    }

    public function update(Request $request, Appointment $appointment)
    {
        if (!auth()->user()->can('edit_appointment')) {
            abort(403, 'You do not have permission to edit appointments.');
        }
        
        // Check ownership for manage_own permission
        if (!auth()->user()->can('manage_all_appointment') && 
            auth()->user()->can('manage_own_appointment') && 
            $appointment->created_by !== auth()->id()) {
            abort(403, 'You can only edit appointments you created.');
        }
        
        $request->validate([
            'doctor_id' => 'nullable|integer',
            'patient_id' => 'nullable|integer',
            'date' => 'required|date',
            'time' => 'required|time',
            'status' => 'required',

        ]);

        $appointment->update($request->all());

        return redirect()->back()->with('success', 'Appointment updated successfully.');
    }

    public function destroy(Appointment $appointment)
    {
        if (!auth()->user()->can('delete_appointment')) {
            abort(403, 'You do not have permission to delete appointments.');
        }
        
        // Check ownership for manage_own permission
        if (!auth()->user()->can('manage_all_appointment') && 
            auth()->user()->can('manage_own_appointment') && 
            $appointment->created_by !== auth()->id()) {
            abort(403, 'You can only delete appointments you created.');
        }
        
        $appointment->delete();

        return redirect()->back()->with('success', 'Appointment deleted successfully.');
    }

}
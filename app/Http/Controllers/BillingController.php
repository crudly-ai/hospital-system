<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Patient;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Yajra\DataTables\Facades\DataTables;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->can('view_billing')) {
            abort(403, 'You do not have permission to view billings.');
        }
        
        if (($request->wantsJson() || $request->ajax()) && !$request->header('X-Inertia')) {
            $billings = Billing::select('billings.*')->with(['patient']);
            
            // Apply ownership filter
            if (auth()->user()->can('manage_all_billing')) {
                // User can see all billings
            } elseif (auth()->user()->can('manage_own_billing')) {
                $billings->where('created_by', auth()->id());
            }
            
            // Apply patient_id filter
            if ($request->filled('patient_id_filter')) {
                $billings->where('patient_id', $request->patient_id_filter);
            }
            
            // Apply amount currency range filter
            if ($request->filled('amount_min')) {
                $billings->where('amount', '>=', $request->amount_min);
            }
            if ($request->filled('amount_max')) {
                $billings->where('amount', '<=', $request->amount_max);
            }
            
            // Apply payment_date date range filter
            if ($request->filled('payment_date_from')) {
                $billings->whereDate('payment_date', '>=', $request->payment_date_from);
            }
            if ($request->filled('payment_date_to')) {
                $billings->whereDate('payment_date', '<=', $request->payment_date_to);
            }
            
            // Apply status filter
            if ($request->filled('status_filter')) {
                $billings->where('status', $request->status_filter);
            }
            
            // Apply date range filter
            if ($request->filled('date_from')) {
                $billings->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $billings->whereDate('created_at', '<=', $request->date_to);
            }

            
            return DataTables::of($billings)
                ->addColumn('created_at_formatted', function ($billing) {
                    return $billing->created_at->format('Y-m-d H:i:s');
                })
                ->addColumn('patient_name', function ($billing) {
                    return $billing->patient ? $billing->patient->name : null;
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



        return Inertia::render('billings/index', [
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

    public function show(Billing $billing)
    {
        if (!auth()->user()->can('view_billing')) {
            abort(403, 'You do not have permission to view this billing.');
        }

        $billing->load(['patient']);
        return response()->json($billing);
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('create_billing')) {
            abort(403, 'You do not have permission to create billings.');
        }
        
        $request->validate([
            'patient_id' => 'nullable|integer',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'status' => 'required',

        ]);

        Billing::create(array_merge($request->all(), ['created_by' => auth()->id()]));

        return redirect()->back()->with('success', 'Billing created successfully.');
    }

    public function update(Request $request, Billing $billing)
    {
        if (!auth()->user()->can('edit_billing')) {
            abort(403, 'You do not have permission to edit billings.');
        }
        
        // Check ownership for manage_own permission
        if (!auth()->user()->can('manage_all_billing') && 
            auth()->user()->can('manage_own_billing') && 
            $billing->created_by !== auth()->id()) {
            abort(403, 'You can only edit billings you created.');
        }
        
        $request->validate([
            'patient_id' => 'nullable|integer',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'status' => 'required',

        ]);

        $billing->update($request->all());

        return redirect()->back()->with('success', 'Billing updated successfully.');
    }

    public function destroy(Billing $billing)
    {
        if (!auth()->user()->can('delete_billing')) {
            abort(403, 'You do not have permission to delete billings.');
        }
        
        // Check ownership for manage_own permission
        if (!auth()->user()->can('manage_all_billing') && 
            auth()->user()->can('manage_own_billing') && 
            $billing->created_by !== auth()->id()) {
            abort(403, 'You can only delete billings you created.');
        }
        
        $billing->delete();

        return redirect()->back()->with('success', 'Billing deleted successfully.');
    }

}
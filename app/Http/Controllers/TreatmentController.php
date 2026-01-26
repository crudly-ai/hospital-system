<?php

namespace App\Http\Controllers;

use App\Models\Treatment;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Yajra\DataTables\Facades\DataTables;

class TreatmentController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->can('view_treatment')) {
            abort(403, 'You do not have permission to view treatments.');
        }
        
        if (($request->wantsJson() || $request->ajax()) && !$request->header('X-Inertia')) {
            $treatments = Treatment::select('treatments.*');
            
            // Apply ownership filter
            if (auth()->user()->can('manage_all_treatment')) {
                // User can see all treatments
            } elseif (auth()->user()->can('manage_own_treatment')) {
                $treatments->where('created_by', auth()->id());
            }
            
            // Apply cost currency range filter
            if ($request->filled('cost_min')) {
                $treatments->where('cost', '>=', $request->cost_min);
            }
            if ($request->filled('cost_max')) {
                $treatments->where('cost', '<=', $request->cost_max);
            }
            
            // Apply status filter
            if ($request->filled('status_filter')) {
                $treatments->where('status', $request->status_filter);
            }
            
            // Apply date range filter
            if ($request->filled('date_from')) {
                $treatments->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $treatments->whereDate('created_at', '<=', $request->date_to);
            }

            
            return DataTables::of($treatments)
                ->addColumn('created_at_formatted', function ($treatment) {
                    return $treatment->created_at->format('Y-m-d H:i:s');
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



        return Inertia::render('treatments/index', [
            'statusOptions' => $statusOptions,


            'currencySettings' => [
                'currency_symbol' => \App\Models\SystemSetting::get('currency_symbol', '$'),
                'currency_position' => \App\Models\SystemSetting::get('currency_position', 'before'),
                'decimal_separator' => \App\Models\SystemSetting::get('decimal_separator', '.'),
                'thousand_separator' => \App\Models\SystemSetting::get('thousand_separator', ','),
            ],
        ]);
    }

    public function show(Treatment $treatment)
    {
        if (!auth()->user()->can('view_treatment')) {
            abort(403, 'You do not have permission to view this treatment.');
        }

        $treatment->load([]);
        return response()->json($treatment);
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('create_treatment')) {
            abort(403, 'You do not have permission to create treatments.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
            'status' => 'required',

        ]);

        Treatment::create(array_merge($request->all(), ['created_by' => auth()->id()]));

        return redirect()->back()->with('success', 'Treatment created successfully.');
    }

    public function update(Request $request, Treatment $treatment)
    {
        if (!auth()->user()->can('edit_treatment')) {
            abort(403, 'You do not have permission to edit treatments.');
        }
        
        // Check ownership for manage_own permission
        if (!auth()->user()->can('manage_all_treatment') && 
            auth()->user()->can('manage_own_treatment') && 
            $treatment->created_by !== auth()->id()) {
            abort(403, 'You can only edit treatments you created.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
            'status' => 'required',

        ]);

        $treatment->update($request->all());

        return redirect()->back()->with('success', 'Treatment updated successfully.');
    }

    public function destroy(Treatment $treatment)
    {
        if (!auth()->user()->can('delete_treatment')) {
            abort(403, 'You do not have permission to delete treatments.');
        }
        
        // Check ownership for manage_own permission
        if (!auth()->user()->can('manage_all_treatment') && 
            auth()->user()->can('manage_own_treatment') && 
            $treatment->created_by !== auth()->id()) {
            abort(403, 'You can only delete treatments you created.');
        }
        
        $treatment->delete();

        return redirect()->back()->with('success', 'Treatment deleted successfully.');
    }

}
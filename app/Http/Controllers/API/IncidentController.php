<?php

namespace App\Http\Controllers\API;  // Make sure this namespace is correct

use App\Http\Controllers\Controller;
use App\Models\Incident;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IncidentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Incident::with('driver.user');
        
        // Apply filters
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        
        // Apply date range filter
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        
        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        
        // Apply search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('driver.user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        $incidents = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();
        
        return Inertia::render('Incidents/Index', [
            'incidents' => $incidents,
            'filters' => $request->only(['status', 'type', 'search', 'from_date', 'to_date']),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Incident $incident)
    {
        $incident->load('driver.user');
        
        return Inertia::render('Incidents/Show', [
            'incident' => $incident,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Incident $incident)
    {
        $incident->load('driver.user');
        
        return Inertia::render('Incidents/Edit', [
            'incident' => $incident,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Incident $incident)
    {
        $validated = $request->validate([
            'status' => 'required|in:reported,in_progress,resolved,closed',
            'resolution_notes' => 'nullable|string',
        ]);
        
        // If status changed to resolved, update resolved_at timestamp
        if ($validated['status'] === 'resolved' && $incident->status !== 'resolved') {
            $validated['resolved_at'] = now();
        }
        
        $incident->update($validated);
        
        return redirect()->route('incidents.index')
            ->with('success', 'Incident updated successfully.');
    }
}
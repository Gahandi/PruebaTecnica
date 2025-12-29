<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TypeEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\ActivityLog;

class TypeEventController extends Controller
{
    public function index()
    {
        $typeEvents = TypeEvent::withCount('events')->get();
        return view('admin.type_events.index', compact('typeEvents'));
    }

    public function create()
    {
        return view('admin.type_events.create');
    }

    public function edit(TypeEvent $typeEvent)
    {
        return view('admin.type_events.edit', compact('typeEvent'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('type_events', 'public');
        }

        $typeEvent = TypeEvent::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'image' => $imagePath,
        ]);

        ActivityLog::log('created', 'Created Type Event: ' . $typeEvent->name);

        return redirect()->route('admin.type_events.index')->with('success', 'Tipo de evento creado correctamente.');
    }

    public function update(Request $request, TypeEvent $typeEvent)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($typeEvent->image) {
                Storage::disk('public')->delete($typeEvent->image);
            }
            $typeEvent->image = $request->file('image')->store('type_events', 'public');
        }

        $typeEvent->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        ActivityLog::log('updated', 'Updated Type Event: ' . $typeEvent->name);

        return redirect()->route('admin.type_events.index')->with('success', 'Tipo de evento actualizado correctamente.');
    }

    public function destroy(TypeEvent $typeEvent)
    {
        if ($typeEvent->image) {
            Storage::disk('public')->delete($typeEvent->image);
        }
        $name = $typeEvent->name;
        $typeEvent->delete();

        ActivityLog::log('deleted', 'Deleted Type Event: ' . $name);

        return redirect()->route('admin.type_events.index')->with('success', 'Tipo de evento eliminado correctamente.');
    }
}

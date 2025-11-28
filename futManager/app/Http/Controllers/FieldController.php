<?php

namespace App\Http\Controllers;

use App\Http\Requests\FieldRequest;
use App\Models\Field;
use App\Models\Owner;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class FieldController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $fields = Field::with('owner')
            ->withCount('tournaments')
            ->latest('name')
            ->paginate(10);

        return view('fields.index', compact('fields'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $owners = Owner::orderBy('name')->get();

        return view('fields.create', [
            'owners' => $owners,
            'field' => new Field(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FieldRequest $request): RedirectResponse
    {
        Field::create($request->validated());

        return redirect()
            ->route('fields.index')
            ->with('status', __('Cancha creada correctamente.'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Field $field): View
    {
        $owners = Owner::orderBy('name')->get();

        return view('fields.edit', compact('field', 'owners'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FieldRequest $request, Field $field): RedirectResponse
    {
        $field->update($request->validated());

        return redirect()
            ->route('fields.index')
            ->with('status', __('Cancha actualizada correctamente.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Field $field): RedirectResponse
    {
        $field->delete();

        return redirect()
            ->route('fields.index')
            ->with('status', __('La cancha ha sido eliminada.'));
    }
}

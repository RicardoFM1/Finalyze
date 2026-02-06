<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMetaRequest;
use App\Models\Meta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MetaController extends Controller
{
    public function index()
    {
        return Auth::user()->metas()->latest()->get();
    }

    public function store(StoreMetaRequest $request)
    {
        $validated = $request->validated();
        return Auth::user()->metas()->create($validated);
    }

    public function update(StoreMetaRequest $request, $id)
    {
        $meta = Auth::user()->metas()->findOrFail($id);
        $meta->update($request->validated());
        return $meta;
    }

    public function destroy($id)
    {
        $meta = Auth::user()->metas()->findOrFail($id);
        $meta->delete();
        return response()->json(['message' => 'Meta excluída com sucesso.']);
    }
}

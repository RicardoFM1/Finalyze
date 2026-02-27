<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMetaRequest;
use App\Models\Meta;
use App\Servicos\Metas\ListarMetas;
use App\Servicos\Metas\CriarMeta;
use App\Servicos\Metas\EditarMeta;
use App\Servicos\Metas\DeletarMeta;
use App\Servicos\Metas\ReativarMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MetaController extends Controller
{
    public function index(Request $request, ListarMetas $servico)
    {
        return $servico->executar($request->all());
    }

    public function store(StoreMetaRequest $request, CriarMeta $servico)
    {
        return $servico->executar($request->validated());
    }

    public function update(StoreMetaRequest $request, $id, EditarMeta $servico)
    {
        return $servico->executar((int)$id, $request->validated());
    }

    public function destroy($id, DeletarMeta $servico)
    {
        $servico->executar((int)$id);
        return response()->json(['message' => 'Meta movida para inativos.']);
    }

    public function reativar($id, ReativarMeta $servico)
    {
        return response()->json($servico->executar((int)$id));
    }
}

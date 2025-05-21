<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ClienteSemSaldoController extends Controller {

  public function __construct() {
    $this->middleware('auth');
  }

  public function index(Request $request) {
    $instituicoes = DB::table('newcorban_fgts')
      ->select('instituicao')
      ->distinct()
      ->whereNotNull('instituicao')
      ->orderBy('instituicao')
      ->pluck('instituicao');

    $query = DB::table('newcorban_fgts as nf')
      ->select('nf.*', 'c.nome', 'c.telefone', 'c.antecipou', 'c.acompanhamento', 'c.trafego', 'c.tipo', 'c.vendedor')
      ->leftJoin('clientes as c', 'c.cpf', '=', 'nf.cpf');

    $query = $this->getFiltro($request, $query);

    $clientes = new LengthAwarePaginator(collect([]), 0, 30, $request->input('page', 1),
      ['path' => $request->url(), 'query' => $request->query()]
    );

    if($query) {
      $clientes = $query->paginate(30)->appends($request->all());
    }

    return view('clientes.sem-saldo', [
      'clientes' => $clientes,
      'instituicoes' => $instituicoes,
      'request' => $request
    ]);
  }

  public function exportarCsv(Request $request) {
    $query = DB::table('newcorban_fgts as nf')
      ->select('nf.*', 'c.nome', 'c.telefone', 'c.antecipou', 'c.acompanhamento', 'c.trafego', 'c.tipo', 'c.vendedor')
      ->leftJoin('clientes as c', 'c.cpf', '=', 'nf.cpf');

    $query = $this->getFiltro($request, $query);
    if(!$query) {
      return redirect()->route('clientes.sem.saldo')->with('error', 'É necessário aplicar os filtros antes de exportar.');
    }

    $clientes = $query->get();

    $headers = [
      'Content-Type' => 'text/csv',
      'Content-Disposition' => 'attachment; filename="clientes-sem-saldo.csv"',
    ];

    $callback = function () use ($clientes) {
      $handle = fopen('php://output', 'w');
      fputcsv($handle, ['CPF', 'Telefone', 'Nome', 'Carteira', 'Tráfego', 'Tipo', 'Vendedor', 'Última Consulta', 'Instituição']);

      foreach ($clientes as $cliente) {
        fputcsv($handle, [
          $cliente->cpf,
          $cliente->telefone,
          $cliente->nome,
          $cliente->acompanhamento,
          $cliente->trafego,
          $cliente->tipo,
          $cliente->vendedor,
          $cliente->ultima_tentativa,
          $cliente->instituicao
        ]);
      }

      fclose($handle);
    };

    return response()->stream($callback, 200, $headers);
  }

  private function getFiltro(Request $request, \Illuminate\Database\Query\Builder $query) {
    if ($request->filled('campo') && $request->filled('valor')) {
      $query->where($request->campo, 'like', '%' . $request->valor . '%');
    }

    if ($request->filled('data_inicio') && $request->filled('data_fim')) {
      $query->whereBetween('nf.data', [$request->data_inicio, $request->data_fim]);
    } else {
      return false;
    }

    $query
      ->when($request->filled('tipo'), function ($q) use ($request) {
        if ($request->tipo === 'Sem tag') {
          $q->whereNull('c.tipo');
        } else {
          $q->where('c.tipo', $request->tipo);
        }
      })
      ->when($request->filled('trafego'), function ($q) use ($request) {
        if ($request->trafego === 'Sem tag') {
          $q->whereNull('c.trafego');
        } else {
          $q->where('c.trafego', $request->trafego);
        }
      })
      ->when($request->filled('acompanhamento'), function ($q) use ($request) {
        if ($request->acompanhamento === 'Sem tag') {
          $q->whereNull('c.acompanhamento');
        } else {
          $q->where('c.acompanhamento', $request->acompanhamento);
        }
      })
      ->when($request->filled('instituicao'), function ($q) use ($request) {
        if ($request->instituicao === 'Sem tag') {
          $q->whereNull('nf.instituicao');
        } else {
          $q->where('nf.instituicao', $request->instituicao);
        }
      })
      ->where(function ($q) {
        $q->where('nf.flag', 'sem_saldo')
          ->orWhere(function ($q) {
            $q->whereNull('nf.flag')->where('nf.valor_liberado', 0);
          });
      });

    return $query->orderByDesc('nf.ultima_tentativa');
  }

}
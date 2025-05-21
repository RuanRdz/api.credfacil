<x-app-layout>
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

        @php
          $hoje = now()->format('Y-m-d');
        @endphp
        @if (session('error'))
          <div class="mb-4 rounded-lg border border-red-300 bg-red-100 p-4 text-red-800 flex items-center gap-2 text-sm">
            <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 9v2m0 4h.01M12 6a9 9 0 100 18 9 9 0 000-18z" />
            </svg>
            <span>{{ session('error') }}</span>
          </div>
        @endif
        <form method="GET" class="flex flex-wrap items-end gap-4 mb-6">
          {{-- Data Início --}}
          <div>
            <label class="block text-sm text-gray-700">Data Início</label>
            <input type="date" name="data_inicio" value="{{ request('data_inicio', $hoje) }}"
              class="border border-gray-300 rounded px-3 py-1.5 text-sm">
          </div>

          {{-- Data Fim --}}
          <div>
            <label class="block text-sm text-gray-700">Data Fim</label>
            <input type="date" name="data_fim" value="{{ request('data_fim', $hoje) }}"
              class="border border-gray-300 rounded px-3 py-1.5 text-sm">
          </div>

          {{-- Tipo --}}
          <div>
            <label class="block text-sm text-gray-700">Tipo</label>
            <select name="tipo" class="border border-gray-300 rounded px-3 py-1.5 text-sm">
              <option value="">Todos</option>
              <option value="Mini" @selected(request('tipo') == 'Mini')>Mini</option>
              <option value="Master" @selected(request('tipo') == 'Master')>Master</option>
              <option value="Sem tag" @selected(request('tipo') == 'Sem tag')>Sem tag</option>
            </select>
          </div>

          {{-- Tráfego --}}
          <div>
            <label class="block text-sm text-gray-700">Tráfego</label>
            <select name="trafego" class="border border-gray-300 rounded px-3 py-1.5 text-sm">
              <option value="">Todos</option>
              <option value="Pago" @selected(request('trafego') == 'Pago')>Pago</option>
              <option value="Normal" @selected(request('trafego') == 'Normal')>Normal</option>
              <option value="Sem tag" @selected(request('trafego') == 'Sem tag')>Sem tag</option>
            </select>
          </div>

          {{-- Acompanhamento (Carteira) --}}
          <div>
            <label class="block text-sm text-gray-700">Carteira</label>
            <select name="acompanhamento" class="border border-gray-300 rounded px-3 py-1.5 text-sm">
              <option value="">Todos</option>
              <option value="Sim" @selected(request('acompanhamento') == 'Sim')>Sim</option>
              <option value="Não" @selected(request('acompanhamento') == 'Não')>Não</option>
              <option value="Sem tag" @selected(request('acompanhamento') == 'Sem tag')>Sem tag</option>
            </select>
          </div>

          {{-- Instituição --}}
          <div>
            <label class="block text-sm text-gray-700">Instituição</label>
            <select name="instituicao" class="border border-gray-300 rounded px-3 py-1.5 text-sm" style="padding-right:30px !important;">
              <option value="">Todas</option>
              @foreach ($instituicoes as $instituicao)
                <option value="{{ $instituicao }}" @selected(request('instituicao') == $instituicao)>
                  {{ $instituicao }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Botão --}}
          <div>
            <button type="submit"
              class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition">
              Filtrar
            </button>
            <a
              href="{{ route('clientes.sem.saldo.exportar', request()->query()) }}"
              class="ml-2 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition"
            >
              Exportar CSV
            </a>
          </div>
        </form>


        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">CPF</th>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Telefone</th>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Nome</th>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Carteira</th>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Tráfego</th>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Tipo</th>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Vendedor</th>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Últ. Tentativa</th>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Instituição</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              @forelse ($clientes as $cliente)
                <tr class="bg-white">
                  <td class="px-4 py-2 text-sm whitespace-nowrap w-40">{{ $cliente->cpf }}</td>
                  <td class="px-4 py-2 text-sm">{{ $cliente->telefone }}</td>
                  <td class="px-4 py-2 text-sm">{{ $cliente->nome }}</td>
                  <td class="px-4 py-2 text-sm">{{ $cliente->acompanhamento }}</td>
                  <td class="px-4 py-2 text-sm">{{ $cliente->trafego }}</td>
                  <td class="px-4 py-2 text-sm">{{ $cliente->tipo }}</td>
                  <td class="px-4 py-2 text-sm">{{ $cliente->vendedor }}</td>
                  <td class="px-4 py-2 text-sm">
                    {{ \Carbon\Carbon::parse($cliente->ultima_tentativa)->format('d/m/Y') }}
                  </td>
                  <td class="px-4 py-2 text-sm">{{ $cliente->instituicao }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="10" class="px-4 py-4 text-center text-sm text-gray-500">
                    Nenhum cliente encontrado com os filtros aplicados.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-6">
          {{ $clientes->links() }}
        </div>

      </div>
    </div>
  </div>
  <script>
  function atualizarCampoFiltro(tipo) {
    const wrapper = document.getElementById('campo_filtro_wrapper');
    if (tipo) {
      wrapper.style.display = 'block';
    } else {
      wrapper.style.display = 'none';
    }
  }

  // Executa ao carregar a página
  document.addEventListener('DOMContentLoaded', function () {
    atualizarCampoFiltro(document.getElementById('filtro_tipo').value);
  });
  </script>
</x-app-layout>

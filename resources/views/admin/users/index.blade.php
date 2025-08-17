<x-app-layout>
  <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

      <h1 class="text-xl font-semibold mb-6">Usuários</h1>

      @if(session('success'))
        <div class="mb-4 rounded-lg border border-green-300 bg-green-100 p-4 text-green-800 flex items-center gap-2 text-sm">
          {{ session('success') }}
        </div>
      @endif

      <a href="{{ route('users.create') }}" class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
        Criar Novo Usuário
      </a>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-2 text-left font-medium text-gray-700">ID</th>
              <th class="px-4 py-2 text-left font-medium text-gray-700">Nome</th>
              <th class="px-4 py-2 text-left font-medium text-gray-700">Email</th>
              <th class="px-4 py-2 text-left font-medium text-gray-700">Role</th>
              <th class="px-4 py-2 text-left font-medium text-gray-700">Ações</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            @foreach ($users as $user)
              <tr>
                <td class="px-4 py-2 whitespace-nowrap">{{ $user->id }}</td>
                <td class="px-4 py-2 whitespace-nowrap">{{ $user->name }}</td>
                <td class="px-4 py-2 whitespace-nowrap">{{ $user->email }}</td>
                <td class="px-4 py-2 whitespace-nowrap">{{ $user->role }}</td>
                <td class="px-4 py-2 whitespace-nowrap space-x-2">
                  <a href="{{ route('users.edit', $user) }}" class="text-blue-600 hover:underline">Editar</a>
                  <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Confirma exclusão do usuário?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:underline">Remover</button>
                  </form>
                </td>
              </tr>
            @endforeach
            @if ($users->isEmpty())
              <tr>
                <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                  Nenhum usuário encontrado.
                </td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>

      <div class="mt-6">
        {{ $users->links() }}
      </div>
    </div>
  </div>
</x-app-layout>

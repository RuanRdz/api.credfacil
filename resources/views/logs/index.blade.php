@extends('layouts.app')

@section('content')
  <div class="max-w-6xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Log do Laravel</h1>

    @if(session('success'))
      <div class="bg-green-100 text-green-800 p-2 rounded mb-4">
        {{ session('success') }}
      </div>
    @endif

    <form method="POST" action="{{ route('logs.clear') }}">
      @csrf
      <button class="bg-red-600 text-white px-4 py-2 rounded mb-4 hover:bg-red-700" 
              onclick="return confirm('Tem certeza que deseja limpar o log?')">
        Limpar Log
      </button>
    </form>

    <div class="bg-gray-100 p-4 rounded overflow-auto max-h-[70vh] text-sm font-mono whitespace-pre-wrap">
      {!! $log !!}
    </div>
  </div>
@endsection

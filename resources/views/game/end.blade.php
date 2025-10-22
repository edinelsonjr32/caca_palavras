@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto text-center">
        <div class="bg-dark-card rounded-lg shadow-lg p-10">
            <h1 class="text-5xl font-bold text-primary-purple mb-4">Fim de Jogo!</h1>
            ...
            <p class="text-6xl font-extrabold text-white mb-10">{{ number_format($gameSession->total_score, 0, ',', '.') }}
            </p>

            <div class="flex justify-center space-x-4">
                <form action="{{ route('game.start') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="bg-primary-purple border-b-violet-800 btn-duolingo text-white font-bold py-3 px-8 text-lg rounded-xl transition">
                        Jogar Novamente
                    </button>
                </form>
                <a href="{{ route('ranking') }}"
                    class="bg-gray-600 border-b-gray-800 btn-duolingo hover:bg-gray-500 text-white font-bold py-3 px-8 text-lg rounded-xl transition">
                    Ver Ranking
                </a>
            </div>
        </div>
    </div>
@endsection
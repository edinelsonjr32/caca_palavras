@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-center text-4xl font-bold text-primary-purple mb-4">Hall da Fama</h1>
    <p class="text-center text-gray-400 mb-8">Veja os melhores jogadores e a sua posição na competição.</p>

    {{-- 1. PÓDIO DOS CAMPEÕES (COM NOVO ALINHAMENTO) --}}
    @if($podiumPlayers->count() >= 3)
        {{-- Container principal alterado para 'items-center' e usando 'gap' para espaçamento --}}
        <div class="flex justify-center items-center gap-8 mb-12">

            {{-- 1º Lugar --}}
            <div class="text-center">
                {{-- Tamanho do avatar padronizado para w-28 h-28 --}}
                <div class="w-28 h-28 rounded-full bg-amber-400 flex items-center justify-center text-4xl font-bold border-4 border-amber-300 mx-auto">
                    {{ strtoupper(substr($podiumPlayers[0]->user->name, 0, 1)) }}
                </div>
                <h3 class="font-bold text-xl mt-2 text-white">{{ $podiumPlayers[0]->user->name }}</h3>
                <p class="text-amber-200">{{ number_format($podiumPlayers[0]->score, 0, ',', '.') }} pts</p>
                <div class="text-5xl mt-1">🏆</div>
            </div>

            {{-- 2º Lugar --}}
            <div class="text-center">
                {{-- Tamanho do avatar padronizado para w-28 h-28 --}}
                <div class="w-28 h-28 rounded-full bg-slate-500 flex items-center justify-center text-4xl font-bold border-4 border-slate-400 mx-auto">
                    {{ strtoupper(substr($podiumPlayers[1]->user->name, 0, 1)) }}
                </div>
                <h3 class="font-bold text-xl mt-2 text-gray-200">{{ $podiumPlayers[1]->user->name }}</h3>
                <p class="text-slate-300">{{ number_format($podiumPlayers[1]->score, 0, ',', '.') }} pts</p>
                <div class="text-4xl mt-1">🥈</div>
            </div>

            {{-- 3º Lugar --}}
            <div class="text-center">
                {{-- Tamanho do avatar padronizado para w-28 h-28 --}}
                <div class="w-28 h-28 rounded-full bg-orange-700 flex items-center justify-center text-4xl font-bold border-4 border-orange-600 mx-auto">
                    {{ strtoupper(substr($podiumPlayers[2]->user->name, 0, 1)) }}
                </div>
                <h3 class="font-bold text-xl mt-2 text-gray-200">{{ $podiumPlayers[2]->user->name }}</h3>
                <p class="text-orange-400">{{ number_format($podiumPlayers[2]->score, 0, ',', '.') }} pts</p>
                <div class="text-4xl mt-1">🥉</div>
            </div>
        </div>
    @endif

    {{-- 2. SUA POSIÇÃO EM DESTAQUE --}}
    @if($userRank)
        <div class="bg-primary-purple/20 border-2 border-primary-purple rounded-xl p-4 flex items-center justify-between mb-8">
            <div class="flex items-center">
                <span class="text-2xl font-bold text-white mr-4">#{{ $userRank['position'] }}</span>
                <div class="w-12 h-12 rounded-full bg-primary-purple flex items-center justify-center text-2xl font-bold text-white mr-4">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <h4 class="font-bold text-lg text-white">(Sua Posição)</h4>
                    <p class="text-gray-300">{{ number_format($userRank['score'], 0, ',', '.') }} pts</p>
                </div>
            </div>
            <span class="text-gray-300">Nível {{ $userRank['level_reached'] }}</span>
        </div>
    @endif
    
    {{-- 3. LISTA DE CLASSIFICAÇÃO GERAL --}}
    <div class="space-y-2">
        @foreach($rankings as $ranking)
            @php
                $position = ($rankings->currentPage() - 1) * $rankings->perPage() + $loop->iteration + 3;
            @endphp
            <div class="bg-dark-card hover:bg-gray-700 rounded-lg p-3 flex items-center space-x-4 transition
                @if($ranking->user_id === Auth::id()) border-2 border-primary-purple @endif
            ">
                <span class="text-lg font-bold text-gray-400 w-8 text-center">#{{ $position }}</span>
                <div class="w-10 h-10 rounded-full bg-gray-600 flex items-center justify-center text-xl font-bold">
                    {{ strtoupper(substr($ranking->user->name, 0, 1)) }}
                </div>
                <span class="font-bold text-white flex-grow">{{ $ranking->user->name }}</span>
                <span class="font-semibold text-gray-300">Nível {{ $ranking->level_reached }}</span>
                <span class="font-bold text-lg text-primary-purple w-28 text-right">{{ number_format($ranking->score, 0, ',', '.') }} pts</span>
            </div>
        @endforeach
    </div>

    {{-- Links de Paginação --}}
    <div class="mt-8">
        {{ $rankings->links() }}
    </div>

</div>
@endsection
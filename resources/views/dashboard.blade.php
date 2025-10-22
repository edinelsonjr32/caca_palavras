@extends('layouts.app')

@section('content')

    {{-- Adicionando o CSS para a animação pulsante --}}
    <style>
        @keyframes pulse-ring {
            0% {
                box-shadow: 0 0 0 0 rgba(139, 92, 246, 0.6);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(139, 92, 246, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(139, 92, 246, 0);
            }
        }

        .pulse-animate {
            animation: pulse-ring 2s infinite;
        }
    </style>

    <div class="max-w-6xl mx-auto">

        {{-- CARD DE ATIVIDADE "EM CHAMAS" (NOVO) --}}
        <div class="bg-dark-card rounded-2xl shadow-lg p-6 mb-8 text-center">
            <h2 class="text-2xl font-bold text-white flex items-center justify-center">
                <span class="text-3xl mr-2">🔥</span>
                Sequência de {{ $stats['currentStreak'] }} Dias!
            </h2>
            <p class="text-gray-400 mt-1">Continue a sua ofensiva para não perder a chama!</p>

            <div class="flex justify-between text-center mt-6">
                @foreach($weeklyActivities as $activityData)
                    @php
                        $date = $activityData['date'];
                        $activity = $activityData['activity'];
                        $hasPlayed = optional($activity)->has_played_game;
                    @endphp
                    <div class="w-1/7">
                        <p class="font-bold text-gray-300 mb-3">{{ $activityData['day_name_short'] }}</p>

                        <div class="w-12 h-12 mx-auto rounded-full flex items-center justify-center
                                {{-- Lógica para o anel pulsante no dia de hoje --}}
                                @if($date->isToday())
                                    pulse-animate
                                @endif
                            ">
                            {{-- Dia Jogado --}}
                            @if($hasPlayed)
                                <span class="text-4xl">🔥</span>
                                {{-- Dia de Hoje, ainda não jogado --}}
                            @elseif($date->isToday() && !$hasPlayed)
                                <span class="text-4xl text-primary-purple opacity-80">🔥</span>
                                {{-- Dia no Passado, não jogado --}}
                            @elseif($date->isPast() && !$hasPlayed)
                                <svg class="w-8 h-8 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM7.707 7.707a1 1 0 011.414 0L10 8.586l.879-.879a1 1 0 111.414 1.414L11.414 10l.879.879a1 1 0 01-1.414 1.414L10 11.414l-.879.879a1 1 0 01-1.414-1.414L8.586 10 7.707 9.121a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                {{-- Dia Futuro --}}
                            @else
                                <div class="w-8 h-8 rounded-full border-4 border-gray-700"></div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Layout Principal de Duas Colunas --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- COLUNA PRINCIPAL (ESQUERDA) - TRILHA DE NÍVEIS --}}
            <div class="lg:col-span-2">
                <h1 class="text-center lg:text-left text-3xl font-bold text-white mb-8">Sua Jornada</h1>
                <div class="space-y-4">
                    @for ($level = 1; $level <= 100; $level++)
                        @php
                            $isCurrent = ($level == $playerCurrentLevel);
                            $isCompleted = ($level < $playerCurrentLevel);
                            $isLocked = ($level > $playerCurrentLevel);
                        @endphp
                        <div class="w-full">
                            @if ($isCurrent)
                                <form action="{{ route('game.start') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left flex items-center justify-between p-4 rounded-xl bg-primary-purple border-b-violet-800 btn-duolingo transition transform hover:scale-105">
                                        <div class="flex items-center">
                                            <span class="bg-white/30 rounded-full p-2 mr-4">
                                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M4.018 15.132A1.25 1.25 0 006 14.25v-8.5a1.25 1.25 0 00-2.296-.943l-2.5 4.25a1.25 1.25 0 000 .886l2.5 4.25a1.25 1.25 0 00.314.289z">
                                                    </path>
                                                    <path
                                                        d="M15.982 4.868a1.25 1.25 0 00-1.682-.314l-6.25 4.25a1.25 1.25 0 000 2.172l6.25 4.25a1.25 1.25 0 001.978-1.243L11.5 10l4.482-3.889a1.25 1.25 0 00.000-1.243z">
                                                    </path>
                                                </svg>
                                            </span>
                                            <span class="font-bold text-xl text-white">Nível {{ $level }}</span>
                                        </div>
                                        <span class="text-white font-bold text-lg">JOGAR</span>
                                    </button>
                                </form>
                            @endif
                            @if ($isCompleted)
                                <div
                                    class="w-full text-left flex items-center justify-between p-4 rounded-xl bg-green-500/50 cursor-pointer hover:bg-green-500/70 transition">
                                    <div class="flex items-center">
                                        <span class="bg-white/30 rounded-full p-2 mr-4">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </span>
                                        <span class="font-bold text-xl text-white opacity-80">Nível {{ $level }}</span>
                                    </div>
                                    <span class="font-bold text-lg text-white opacity-80">CONCLUÍDO</span>
                                </div>
                            @endif
                            @if ($isLocked)
                                <div class="w-full text-left flex items-center p-4 rounded-xl bg-dark-card opacity-60">
                                    <div class="flex items-center">
                                        <span class="bg-black/20 rounded-full p-2 mr-4">
                                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </span>
                                        <span class="font-bold text-xl text-gray-400">Nível {{ $level }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endfor
                </div>
            </div>

            {{-- COLUNA LATERAL (DIREITA) - WIDGETS --}}
            <div class="space-y-8">
                <div class="bg-dark-card rounded-xl p-6 text-center">
                    <svg class="w-24 h-24 mx-auto text-primary-purple" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 20C9 20.5523 9.44772 21 10 21H14C14.5523 21 15 20.5523 15 20V19H9V20Z"
                            fill="currentColor" />
                        <path
                            d="M12 2C7.85997 2 4.5 5.35997 4.5 9.5C4.5 12.0345 5.7334 14.3311 7.5 15.864V18H16.5V15.864C18.2666 14.3311 19.5 12.0345 19.5 9.5C19.5 5.35997 16.14 2 12 2ZM12 14C11.4477 14 11 13.5523 11 13V11H10C9.44772 11 9 10.5523 9 10C9 9.44772 9.44772 9 10 9H11V7C11 6.44772 11.4477 6 12 6C12.5523 6 13 6.44772 13 7V9H14C14.5523 9 15 9.44772 15 10C15 10.5523 14.5523 11 14 11H13V13C13 13.5523 12.5523 14 12 14Z"
                            fill="currentColor" />
                    </svg>
                    <p class="mt-4 text-gray-300 font-semibold">Continue desafiando sua mente e desbloqueie todos os níveis!
                    </p>
                </div>
                <div class="bg-dark-card rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-white mb-4">Minhas Estatísticas</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-gray-400">🔥 Sequência Atual</span>
                            <span class="font-bold text-2xl text-primary-purple">{{ $stats['currentStreak'] }} dias</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">🏆 Pontuação Máxima</span>
                            <span
                                class="font-bold text-white">{{ number_format($stats['highestScore'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">🎮 Partidas Jogadas</span>
                            <span class="font-bold text-white">{{ $stats['gamesPlayed'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">✅ Níveis Concluídos</span>
                            <span class="font-bold text-white">{{ $stats['levelsCompleted'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="bg-dark-card rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-white mb-4">Conquistas Recentes</h2>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">🥇</span>
                            <span class="text-gray-300">Pioneiro: Completou o Nível 1.</span>
                        </div>
                        <div class="flex items-center opacity-50">
                            <span class="text-2xl mr-3">🗓️</span>
                            <span class="text-gray-400">Maratonista: Jogou por 5 dias seguidos.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
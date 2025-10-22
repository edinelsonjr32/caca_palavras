<nav class="bg-dark-card shadow-lg">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between py-3">
            {{-- Lado Esquerdo: Logo --}}
            <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-primary-purple">
                CAÇA-PALAVRAS
            </a>

            {{-- Lado Direito: Recursos e Avatar --}}
            @auth
            <div class="flex items-center space-x-4 md:space-x-6">
                {{-- Link para o Ranking --}}
                <a href="{{ route('ranking') }}" class="hidden md:flex items-center space-x-2 text-gray-300 hover:text-white transition">
                    <span class="text-2xl">🏆</span>
                    <span class="font-bold">Ranking</span>
                </a>

                {{-- Contador de Streak --}}
                <div class="bg-black/20 rounded-lg flex items-center space-x-2 px-3 py-2">
                    <span class="text-2xl">🔥</span>
                    <span class="font-bold text-white text-lg">{{ $currentStreak ?? 0 }}</span>
                </div>

                {{-- Contador de Vidas (Exemplo) --}}
                <div class="bg-black/20 rounded-lg flex items-center space-x-2 px-3 py-2">
                    <span class="text-2xl text-red-500">❤️</span>
                    <span class="font-bold text-white text-lg">{{ $userHearts ?? '...' }}</span>
                </div>

                {{-- Avatar e Menu Dropdown --}}
                <div class="relative">
                    {{-- Botão do Avatar --}}
                    <button id="avatar-button" class="w-12 h-12 rounded-full bg-primary-purple flex items-center justify-center text-white text-2xl font-bold focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-dark-card focus:ring-white overflow-hidden">
                        {{-- MELHORIA: Mostra o avatar se existir, senão mostra a inicial --}}
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        @endif
                    </button>

                    {{-- Menu Dropdown (escondido) --}}
                    <div id="dropdown-menu" class="absolute right-0 mt-2 w-48 bg-dark-bg border border-dark-border rounded-lg shadow-xl z-20 hidden">
                        <div class="p-2">
                            {{-- CORREÇÃO: O link 'href' foi atualizado --}}
                            <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-300 rounded hover:bg-primary-purple hover:text-white">Meu Perfil</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-300 rounded hover:bg-primary-purple hover:text-white">Sair</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endauth
        </div>
    </div>
</nav>

{{-- Script para controlar o dropdown --}}
<script>
    if (document.getElementById('avatar-button')) {
        const avatarButton = document.getElementById('avatar-button');
        const dropdownMenu = document.getElementById('dropdown-menu');

        avatarButton.addEventListener('click', (event) => {
            event.stopPropagation(); // Impede que o clique se propague para o window
            dropdownMenu.classList.toggle('hidden');
        });

        window.addEventListener('click', (event) => {
            if (!dropdownMenu.contains(event.target) && !avatarButton.contains(event.target)) {
                dropdownMenu.classList.add('hidden');
            }
        });
    }
</script>
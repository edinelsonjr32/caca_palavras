<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | WordQuest</title>
    
    {{-- GARANTIA DE ESTILO: Se você usa Laravel 9+ com Vite, use esta linha: --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Se você usa Laravel Mix (versões mais antigas), use: --}}
    {{-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> --}}

    {{-- Incluindo Font Awesome (para ícones) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLMDJ87uwNmoPz2z8/U52gqF6L/t+eFk5R/B5U1e4e6yK0K1b1b1g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="bg-gray-900 antialiased">

    <div class="min-h-screen flex items-center justify-center bg-gray-900 p-4">
        <div class="w-full max-w-md">
            {{-- Card de Login: Consistente com o estilo da Landing Page --}}
            <div class="bg-gray-800 shadow-2xl rounded-xl px-8 pt-8 pb-10 border-t-4 border-violet-600">
                
                {{-- Ícone e Título --}}
                <div class="text-center mb-6">
                    <i class="fas fa-gamepad text-violet-500 text-4xl mb-3"></i>
                    <h1 class="text-3xl font-extrabold text-white">
                        Acesso ao Jogo
                    </h1>
                    <p class="text-gray-400 text-sm mt-1">Bem-vindo de volta, Aventureiro!</p>
                </div>
                
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    {{-- Campo Email --}}
                    <div class="mb-5">
                        <label class="block text-gray-300 text-sm font-semibold mb-2" for="email">
                            <i class="fas fa-envelope text-violet-400 mr-2"></i>Email
                        </label>
                        <input
                            class="shadow appearance-none border border-gray-700 rounded-lg w-full py-3 px-4 bg-gray-700 text-white leading-tight focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition duration-200"
                            id="email" 
                            type="email" 
                            name="email" 
                            placeholder="seu@email.com"
                            value="{{ old('email') }}"
                            required autofocus>
                        @error('email')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    {{-- Campo Senha --}}
                    <div class="mb-6">
                        <label class="block text-gray-300 text-sm font-semibold mb-2" for="password">
                            <i class="fas fa-lock text-violet-400 mr-2"></i>Senha
                        </label>
                        <input
                            class="shadow appearance-none border border-gray-700 rounded-lg w-full py-3 px-4 bg-gray-700 text-white leading-tight focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition duration-200"
                            id="password" 
                            type="password" 
                            name="password" 
                            placeholder="••••••••"
                            required>
                        @error('password')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    {{-- Botão Entrar --}}
                    <div class="flex items-center justify-between">
                        <button
                            class="flex items-center justify-center space-x-2 bg-violet-600 hover:bg-violet-700 text-white font-extrabold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline w-full text-lg shadow-lg shadow-violet-500/50 transition duration-200 transform hover:scale-[1.02]"
                            type="submit">
                             <i class="fas fa-arrow-right-to-bracket"></i> <span>ENTRAR</span>
                        </button>
                    </div>
                    
                    {{-- Link para Cadastro --}}
                    <p class="text-center text-gray-400 text-sm mt-6">
                        Não tem uma conta? <a href="{{ route('register') }}"
                            class="text-violet-400 hover:text-violet-300 font-bold hover:underline transition duration-200">Cadastre-se</a>
                    </p>
                    
                </form>
            </div>
        </div>
    </div>

</body>
</html>
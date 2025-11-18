<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro | FastLex</title>

    {{-- GARANTIA DE ESTILO: Se você usa Laravel 9+ com Vite, use esta linha: --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Incluindo Font Awesome (para ícones) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLMDJ87uwNmoPz2z8/U52gqF6L/t+eFk5R/B5U1e4e6yK0K1b1b1g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="bg-gray-900 antialiased">

    <div class="min-h-screen flex items-center justify-center bg-gray-900 p-4">
        <div class="w-full max-w-md">
            {{-- Card de Cadastro: Consistente com o estilo da Landing Page --}}
            <div class="bg-gray-800 shadow-2xl rounded-xl px-8 pt-8 pb-10 border-t-4 border-violet-600">

                {{-- Área do Título / Logo --}}
                <div class="text-center mb-6">
                    
                    {{-- 🟢 INSERÇÃO DA LOGO AQUI --}}
                    <div class="flex justify-center mb-3">
                        <img 
                            src="{{ asset('logo_fastlex.png') }}" 
                            alt="Logo Word Quest" 
                            class="h-16 w-auto transition duration-300"
                            style="filter: drop-shadow(0 0 5px rgba(139, 92, 246, 0.4));">
                    </div>
                    {{-- 🔴 REMOÇÃO DO ÍCONE E TÍTULO ANTIGOS --}}

                    <h1 class="text-2xl font-extrabold text-white">
                        Crie Sua Conta
                    </h1>
                    <p class="text-gray-400 text-sm mt-1">Junte-se à aventura e aprenda inglês!</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    {{-- Campo Nome --}}
                    <div class="mb-5">
                        <label class="block text-gray-300 text-sm font-semibold mb-2" for="name">
                            <i class="fas fa-user text-violet-400 mr-2"></i>Nome de Usuário
                        </label>
                        <input
                            class="shadow appearance-none border border-gray-700 rounded-lg w-full py-3 px-4 bg-gray-700 text-white leading-tight focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition duration-200"
                            id="name" type="text" name="name" placeholder="Seu nome no jogo" value="{{ old('name') }}"
                            required autofocus>
                        @error('name')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Campo Email --}}
                    <div class="mb-5">
                        <label class="block text-gray-300 text-sm font-semibold mb-2" for="email">
                            <i class="fas fa-envelope text-violet-400 mr-2"></i>Email
                        </label>
                        <input
                            class="shadow appearance-none border border-gray-700 rounded-lg w-full py-3 px-4 bg-gray-700 text-white leading-tight focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition duration-200"
                            id="email" type="email" name="email" placeholder="seu@email.com" value="{{ old('email') }}"
                            required>
                        @error('email')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Campo Senha --}}
                    <div class="mb-5">
                        <label class="block text-gray-300 text-sm font-semibold mb-2" for="password">
                            <i class="fas fa-lock text-violet-400 mr-2"></i>Senha
                        </label>
                        <input
                            class="shadow appearance-none border border-gray-700 rounded-lg w-full py-3 px-4 bg-gray-700 text-white leading-tight focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition duration-200"
                            id="password" type="password" name="password" placeholder="Mínimo 8 caracteres" required>
                        @error('password')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Campo Confirmar Senha --}}
                    <div class="mb-6">
                        <label class="block text-gray-300 text-sm font-semibold mb-2" for="password_confirmation">
                            <i class="fas fa-lock text-violet-400 mr-2"></i>Confirmar Senha
                        </label>
                        <input
                            class="shadow appearance-none border border-gray-700 rounded-lg w-full py-3 px-4 bg-gray-700 text-white leading-tight focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition duration-200"
                            id="password_confirmation" type="password" name="password_confirmation"
                            placeholder="Repita a senha" required>
                    </div>

                    {{-- Botão Cadastrar --}}
                    <div class="flex items-center justify-between">
                        <button
                            class="flex items-center justify-center space-x-2 bg-violet-600 hover:bg-violet-700 text-white font-extrabold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline w-full text-lg shadow-lg shadow-violet-500/50 transition duration-200 transform hover:scale-[1.02]"
                            type="submit">
                            <i class="fas fa-user-plus"></i> <span>CADASTRAR</span>
                        </button>
                    </div>

                    {{-- Link para Login --}}
                    <p class="text-center text-gray-400 text-sm mt-6">
                        Já tem uma conta? <a href="{{ route('login') }}"
                            class="text-violet-400 hover:text-violet-300 font-bold hover:underline transition duration-200">Faça
                            Login</a>
                    </p>

                </form>
            </div>
        </div>
    </div>

</body>

</html>
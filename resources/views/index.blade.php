<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WordQuest | Aprenda Inglês Jogando!</title>
    
    {{-- GARANTIA DE ESTILO: Se você usa Laravel 9+ com Vite, use esta linha: --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- 1. Incluindo Font Awesome (para ícones) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLMDJ87uwNmoPz2z8/U52gqF6L/t+eFk5R/B5U1e4e6yK0K1b1b1g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    {{-- 2. Estilo e Animações customizadas INCLUÍDAS diretamente no <head> --}}
    <style>
        /* Define a animação para os "blobs" de fundo (garante que funcione) */
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob {
            animation: blob 7s infinite cubic-bezier(0.6, -0.28, 0.735, 0.045);
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
    </style>
</head>
<body class="bg-gray-900 antialiased">

    {{-- Seção Hero (Destaque Principal) --}}
    <section class="min-h-screen flex flex-col items-center justify-center text-center px-4 py-20 relative overflow-hidden">
        
        {{-- Efeito de Fundo Roxo --}}
        <div class="absolute inset-0 z-0 opacity-10 pointer-events-none">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-violet-600 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
            <div class="absolute bottom-0 right-1/4 w-80 h-80 bg-fuchsia-600 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
        </div>

        <div class="relative z-10 max-w-4xl mx-auto">
            
            {{-- Ícone de Destaque --}}
            <i class="fas fa-brain text-violet-500 text-5xl mb-4 animate-bounce"></i>

            {{-- Título Principal: LOGO COM O DOBRO DO TAMANHO E BOTÕES MENOS ARREDONDADOS --}}
            <div class="flex justify-center items-center mb-6">
                <img 
                    src="{{ asset('logo_fastlex.png') }}" 
                    alt="Logo Word Quest" 
                    class="h-64 sm:h-80 transition duration-300 transform hover:scale-105" 
                    style="filter: drop-shadow(0 0 10px rgba(139, 92, 246, 0.7));">
            </div>

            {{-- Subtítulo Chamativo --}}
            <p class="text-xl sm:text-2xl text-gray-300 font-light mb-10">
                O jeito mais <span class="text-violet-400 font-bold">divertido e rápido</span> de dominar o vocabulário <span class="text-yellow-400 font-bold">essencial em inglês</span>.
            </p>

            {{-- Área de Ação (CTA - Call To Action) --}}
            <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-6">
                
                {{-- Botão Principal: JOGAR AGORA (rounded-2xl) --}}
                <a href="{{ route('login') }}" 
                    class="flex items-center justify-center space-x-2 bg-violet-600 hover:bg-violet-700 text-white font-extrabold py-3 px-10 rounded-2xl text-xl shadow-2xl shadow-violet-500/50 transition duration-300 transform hover:scale-105 hover:shadow-violet-400/70 uppercase tracking-wider">
                    <i class="fas fa-play"></i> 
                    <span>JOGAR AGORA!</span>
                </a>
                
                {{-- Botão Secundário: COMO FUNCIONA (rounded-2xl) --}}
                <a href="#how-it-works" 
                    class="flex items-center justify-center space-x-2 bg-gray-700 hover:bg-gray-600 text-gray-200 border-2 border-violet-500 font-semibold py-3 px-10 rounded-2xl text-xl transition duration-300 transform hover:scale-105 uppercase tracking-wider">
                    <i class="fas fa-info-circle"></i>
                    <span>Como Funciona?</span>
                </a>
            </div>

            {{-- Elemento de Confiança/Status --}}
            <div class="mt-12 text-gray-400 text-sm">
                <p><i class="fas fa-bolt text-yellow-400"></i> + de 100 níveis desafiadores. Comece hoje!</p>
            </div>
            
        </div>
    </section>

    {{-- Seção de Destaque "Como Funciona" --}}
    <section id="how-it-works" class="py-20 bg-gray-800 border-t border-gray-700">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold text-white mb-12">
                Domine o Inglês em 3 Passos
            </h2>
            <div class="grid md:grid-cols-3 gap-8">
                
                {{-- Passo 1 --}}
                <div class="bg-gray-900 p-6 rounded-lg shadow-xl border-b-4 border-violet-500 transform hover:translate-y-[-5px] transition duration-300">
                    <i class="fas fa-user-plus text-violet-500 text-3xl mb-3"></i>
                    <h3 class="text-xl font-semibold text-white mb-3">Cadastre-se</h3>
                    <p class="text-gray-400">Crie sua conta em segundos e comece sua jornada de aprendizado.</p>
                </div>

                {{-- Passo 2 --}}
                <div class="bg-gray-900 p-6 rounded-lg shadow-xl border-b-4 border-yellow-400 transform hover:translate-y-[-5px] transition duration-300">
                    <i class="fas fa-search text-yellow-400 text-3xl mb-3"></i>
                    <h3 class="text-xl font-semibold text-white mb-3">Encontre as Palavras</h3>
                    <p class="text-gray-400">Jogue centenas de caça-palavras com vocabulário essencial.</p>
                </div>

                {{-- Passo 3 --}}
                <div class="bg-gray-900 p-6 rounded-lg shadow-xl border-b-4 border-fuchsia-500 transform hover:translate-y-[-5px] transition duration-300">
                    <i class="fas fa-trophy text-fuchsia-500 text-3xl mb-3"></i>
                    <h3 class="text-xl font-semibold text-white mb-3">Avance e Aprenda</h3>
                    <p class="text-gray-400">Suba de nível, ganhe recompensas e desbloqueie novos desafios.</p>
                </div>
            </div>
        </div>
    </section>

</body>
</html>
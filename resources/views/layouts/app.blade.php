<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caça-Palavras Black & Purple</title>
    {{-- Importando o Tailwind CSS via CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Configuração customizada para o novo tema Black & Purple
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-purple': '#8B5CF6', // Roxo Vibrante
                        'dark-bg': '#111827',      // Fundo escuro
                        'dark-card': '#1f2937',    // Cor do card
                        'dark-border': '#374151',  // Cor da borda
                    }
                }
            }
        }
    </script>
    <style>
        body {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Estilo customizado para o botão "3D" do Duolingo */
        .btn-duolingo {
            border-bottom-width: 4px;
        }

        .btn-duolingo:active {
            transform: translateY(2px);
            border-bottom-width: 2px;
        }
    </style>
</head>

<body class="bg-dark-bg text-gray-200">
    <div id="app">
        @include('partials.navbar')

        <main class="py-10">
            <div class="container mx-auto px-4">
                @yield('content')
            </div>
        </main>
    </div>
</body>

</html>
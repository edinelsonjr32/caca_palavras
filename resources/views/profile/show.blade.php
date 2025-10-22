@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto">
        <h1 class="text-center text-4xl font-bold text-primary-purple mb-8">Meu Perfil</h1>

        <div class="bg-dark-card shadow-lg rounded-xl p-6 md:p-8">

            {{-- Mensagem de sucesso --}}
            @if (session('status'))
                <div class="bg-green-500/20 border-l-4 border-green-500 text-green-300 p-4 mb-6 rounded-r-lg" role="alert">
                    <p>{{ session('status') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-center">
                {{-- Coluna do Avatar e Upload --}}
                <div class="md:col-span-1 text-center">
                    {{-- Pré-visualização do Avatar --}}
                    <div
                        class="w-40 h-40 rounded-full bg-gray-600 mx-auto mb-4 flex items-center justify-center text-6xl font-bold text-white overflow-hidden">
                        @if (Auth::user()->avatar)
                            <img id="avatar-preview" src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar"
                                class="w-full h-full object-cover">
                        @else
                            <span id="avatar-initials">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                            <img id="avatar-preview" src="" alt="Avatar Preview" class="w-full h-full object-cover hidden">
                        @endif
                    </div>

                    <form action="{{ route('profile.avatar.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label for="avatar-upload"
                            class="cursor-pointer bg-gray-600 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-lg inline-block transition">
                            Escolher Imagem
                        </label>
                        <input id="avatar-upload" name="avatar" type="file" class="hidden">

                        <button type="submit"
                            class="mt-4 bg-primary-purple hover:bg-violet-500 text-white font-bold py-2 px-6 rounded-lg transition">
                            Salvar Avatar
                        </button>

                        @error('avatar')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </form>
                </div>

                {{-- Coluna de Informações do Usuário --}}
                <div class="md:col-span-2">
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-bold text-gray-400">Nome</label>
                            <p class="text-xl text-white">{{ Auth::user()->name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-bold text-gray-400">Email</label>
                            <p class="text-xl text-white">{{ Auth::user()->email }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-bold text-gray-400">Membro Desde</label>
                            <p class="text-xl text-white">
                                {{ Auth::user()->created_at->translatedFormat('d \d\e F \d\e Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const avatarUpload = document.getElementById('avatar-upload');
        const avatarPreview = document.getElementById('avatar-preview');
        const avatarInitials = document.getElementById('avatar-initials');

        avatarUpload.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    avatarPreview.src = e.target.result;
                    avatarPreview.classList.remove('hidden');
                    if (avatarInitials) {
                        avatarInitials.classList.add('hidden');
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
@endsection
<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show()
    {
        return view('profile.show', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Lida com o upload e a atualização do avatar do usuário.
     */
    public function updateAvatar(Request $request)
    {
        // 1. Validação do arquivo
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Requerido, imagem, tipos permitidos, tamanho máximo de 2MB
        ]);

        $user = Auth::user();

        // 2. Deletar o avatar antigo, se existir
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // 3. Salvar o novo avatar e obter o caminho
        $path = $request->file('avatar')->store('avatars', 'public');

        // 4. Atualizar o registro do usuário com o novo caminho
        $user->update(['avatar' => $path]);

        // 5. Redirecionar de volta para o perfil com uma mensagem de sucesso
        return redirect()->route('profile.show')->with('status', 'Avatar atualizado com sucesso!');
    }
}

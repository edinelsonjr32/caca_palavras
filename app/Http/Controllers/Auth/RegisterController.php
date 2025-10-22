<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create()
    {
        // Esta função simplesmente retorna a view que criaremos mais tarde
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request)
    {
        // 1. Validar os dados do formulário
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. Criar o novo usuário no banco de dados
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 3. Logar o usuário recém-criado
        Auth::login($user);

        // 4. Redirecionar para o dashboard
        return redirect()->route('dashboard');
    }
}

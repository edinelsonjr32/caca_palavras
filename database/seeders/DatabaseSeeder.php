<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Level;
use App\Models\Word;
use App\Models\Ranking;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criar um usuário de teste principal
        $mainUser = User::factory()->create([
            'name' => 'Jogador Teste',
            'email' => 'jogador@teste.com',
            'password' => Hash::make('123456'),
        ]);

        // Criar mais 10 usuários aleatórios
        $otherUsers = User::factory(10)->create();

        // Lista de palavras para os níveis
        // Lista EXPANDIDA de palavras essenciais em inglês para os níveis
        // Inclui substantivos, verbos, adjetivos e advérbios de alta frequência.
        $wordList = [
            // Substantivos Comuns
            'TIME',
            'PEOPLE',
            'WAY',
            'WATER',
            'FOOD',
            'HOME',
            'HAND',
            'CITY',
            'WORLD',
            'LIFE',
            'THING',
            'YEAR',
            'DAY',
            'MAN',
            'WOMAN',
            'CHILD',
            'FAMILY',
            'WORK',
            'GOV',
            'PART',
            'PLACE',
            'CASE',
            'POINT',
            'GROUP',
            'SYSTEM',
            'NUMBER',
            'PROBLEM',
            'NIGHT',
            'WEEK',
            'MONTH',
            'SCHOOL',
            'ROOM',
            'WORD',
            'BOOK',
            'MONEY',
            'ART',
            'FACT',
            'IDEA',
            'KIND',
            'LINE',

            // Verbos Comuns
            'BE',
            'HAVE',
            'DO',
            'SAY',
            'GO',
            'GET',
            'MAKE',
            'KNOW',
            'THINK',
            'TAKE',
            'SEE',
            'COME',
            'WANT',
            'USE',
            'FIND',
            'GIVE',
            'TELL',
            'WORK',
            'CALL',
            'TRY',
            'ASK',
            'NEED',
            'FEEL',
            'BECOME',
            'LEAVE',
            'PUT',
            'MEAN',
            'KEEP',
            'LET',
            'BEGIN',
            'SEEM',
            'HELP',
            'SHOW',
            'HEAR',
            'PLAY',
            'RUN',
            'MOVE',
            'LIVE',
            'BELIEVE',
            'HOLD',

            // Adjetivos Essenciais
            'GOOD',
            'NEW',
            'FIRST',
            'LAST',
            'LONG',
            'GREAT',
            'LITTLE',
            'OWN',
            'OTHER',
            'RIGHT',
            'BIG',
            'HIGH',
            'SMALL',
            'LARGE',
            'OLD',
            'DIFFERENT',
            'LOCAL',
            'PUBLIC',
            'MAIN',
            'SURE',
            'BEST',
            'FREE',
            'FAST',
            'SLOW',
            'HAPPY',
            'READY',
            'EASY',
            'DARK',
            'OPEN',
            'CLOSE',

            // Cores e Direções (úteis para vocabulário básico)
            'RED',
            'BLUE',
            'GREEN',
            'BLACK',
            'WHITE',
            'YELLOW',
            'LEFT',
            'RIGHT',
            'UP',
            'DOWN',

            // Termos de Caça-Palavras/Aplicativo
            'PROJECT',
            'LEVEL',
            'EASY',
            'HARD',
            'BOARD',
            'SECRET',
            'POINT',
            'SEARCH',
            'CODE',
            'QUERY'
        ];

        // Criar 100 Níveis com palavras
        for ($i = 1; $i <= 100; $i++) {
            // Aumenta o grid e o tempo gradualmente
            $level = Level::create([
                'level_number' => $i,
                'grid_size' => 10 + floor($i / 10), // Aumenta o grid a cada 10 níveis
                'time_limit_seconds' => 180 + ($i * 2), // Aumenta o tempo
            ]);

            // Adicionar de 5 a 10 palavras a cada nível (palavras únicas para o nível)
            // Usamos collect() para ter acesso a shuffle() e take()
            $wordsForLevel = collect($wordList)->shuffle()->take(rand(5, 10));

            foreach ($wordsForLevel as $word) {
                Word::create([
                    'level_id' => $level->id,
                    'word' => $word
                ]);
            }
        }

        // Popular o ranking com os usuários criados
        foreach ($otherUsers as $user) {
            Ranking::factory()->create(['user_id' => $user->id]);
        }
        // Adicionar o usuário principal ao ranking também
        Ranking::factory()->create(['user_id' => $mainUser->id]);
    }
}

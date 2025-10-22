@extends('layouts.app')

@section('content')

{{-- O HTML permanece o mesmo, o foco é no SCRIPT de diagnóstico --}}
<div id="level-complete-modal" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 transition-opacity duration-300 opacity-0 pointer-events-none">
    <div id="modal-content" class="bg-dark-card rounded-2xl shadow-2xl p-8 text-center transform transition-all scale-95">
        <h2 class="text-4xl font-extrabold text-primary-purple mb-4">Nível Concluído!</h2>
        <button id="next-level-button" class="w-full bg-primary-purple border-b-violet-800 btn-duolingo text-white font-bold py-3 px-8 text-xl rounded-xl">Próximo Nível</button>
    </div>
</div>
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-4 px-2">
        <h1 class="text-3xl font-bold text-white">Nível {{ $level->level_number }}</h1>
        <form action="{{ route('game.end') }}" method="GET"><input type="hidden" name="status" value="failed"><button type="submit" class="bg-gray-600/50 hover:bg-gray-600 border-b-gray-800 btn-duolingo text-white font-semibold py-2 px-4 rounded-xl">Desistir</button></form>
    </div>
    <div class="w-full bg-dark-card rounded-full h-4 mb-6 border-2 border-dark-border">
        <div id="progress-bar" class="bg-primary-purple h-full rounded-full transition-all duration-500" style="width: 0%"></div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="md:col-span-2 bg-dark-card p-6 rounded-2xl shadow-lg">
            <div id="word-search-grid" class="grid gap-1 select-none cursor-pointer" style="grid-template-columns: repeat({{ $level->grid_size }}, minmax(0, 1fr));"></div>
        </div>
        <div class="space-y-6">
            <div class="bg-dark-card p-4 rounded-2xl shadow-lg flex items-center justify-between"><span class="text-lg font-semibold text-gray-300">🏆 Pontuação</span><span id="score-display" class="text-2xl font-bold text-white">{{ $gameSession->total_score }}</span></div>
            <div class="bg-dark-card p-4 rounded-2xl shadow-lg flex items-center justify-between"><span class="text-lg font-semibold text-gray-300">⏳ Tempo</span><span id="timer-display" class="text-2xl font-bold bg-red-600 text-white px-3 py-1 rounded-lg">{{ gmdate("i:s", $level->time_limit_seconds) }}</span></div>
            <div class="bg-dark-card p-6 rounded-2xl shadow-lg">
                <h3 class="font-semibold text-white text-xl mb-4 text-center">Palavras</h3>
                <div id="word-list" class="flex flex-wrap gap-3 justify-center">
                    @foreach($secretWords as $word)
                        <div class="word-chip bg-gray-700 text-gray-300 font-semibold py-2 px-4 rounded-lg" data-word="{{ strtoupper($word) }}"><span>{{ strtoupper($word) }}</span></div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .selection-highlight { background-color: #8B5CF6; }
    .found-word { background-color: #16a34a !important; color: white !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // ... (O resto do script é o mesmo da última versão)

    // A única mudança está na função init() abaixo:

    const init = () => {
        console.log("PASSO 1: Iniciando a geração do grid...");

        // Lógica de geração do grid
        state.gridMatrix = Array.from({ length: config.gridSize }, () => Array(config.gridSize).fill(''));
        const directions = [{ r: 0, c: 1 }, { r: 1, c: 0 }, { r: 1, c: 1 }, { r: 0, c: -1 }, { r: -1, c: 0 }, { r: -1, c: -1 }, { r: 1, c: -1 }, { r: -1, c: 1 }];
        config.words.forEach(word => { let placed = false; let attempts = 0; while(!placed && attempts < 100) { attempts++; const dir = directions[Math.floor(Math.random() * directions.length)]; const rStart = Math.floor(Math.random() * config.gridSize); const cStart = Math.floor(Math.random() * config.gridSize); let canPlace = true; for (let i = 0; i < word.length; i++) { const r = rStart + i * dir.r, c = cStart + i * dir.c; if (r < 0 || r >= config.gridSize || c < 0 || c >= config.gridSize || (state.gridMatrix[r][c] !== '' && state.gridMatrix[r][c] !== word[i])) { canPlace = false; break; } } if (canPlace) { for (let i = 0; i < word.length; i++) { state.gridMatrix[rStart + i * dir.r][cStart + i * dir.c] = word[i]; } placed = true; } } });
        const alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        for (let r = 0; r < config.gridSize; r++) { for (let c = 0; c < config.gridSize; c++) { if (state.gridMatrix[r][c] === '') { state.gridMatrix[r][c] = alphabet[Math.floor(Math.random() * alphabet.length)]; } } }

        console.log("PASSO 2: Matriz de letras populada, agora desenhando no HTML...");

        // Desenha o grid no HTML
        ui.grid.innerHTML = state.gridMatrix.map((row) => row.map((letter) => 
            `<div class="grid-cell w-10 h-10 flex items-center justify-center text-xl font-bold bg-gray-700 rounded-md transition-transform">${letter}</div>`
        ).join('')).join('');
        
        console.log("PASSO 3: Grid desenhado com sucesso. Adicionando listeners...");
        
        // Adiciona o listener principal ao grid
        ui.grid.addEventListener('mousedown', startSelection);
        
        // Inicia o cronômetro
        state.timerInterval = setInterval(() => { state.timeLeft--; if (state.timeLeft >= 0) { ui.timer.textContent = new Date(state.timeLeft * 1000).toISOString().substr(14, 5); } else { clearInterval(state.timerInterval); alert('Tempo esgotado!'); window.location.href = `${config.endGameUrl}?status=failed`; } }, 1000);
    };

    // --- Colando o resto do script para garantir que esteja completo ---
    const config = { gridSize: {{ $level->grid_size }}, words: @json($secretWords).map(w => w.toUpperCase()), levelId: {{ $level->id }}, timeLeftInitial: {{ $level->time_limit_seconds }}, validateUrl: "{{ route('game.validate') }}", endGameUrl: "{{ route('game.end') }}", csrfToken: "{{ csrf_token() }}" };
    const ui = { grid: document.getElementById('word-search-grid'), score: document.getElementById('score-display'), timer: document.getElementById('timer-display'), progressBar: document.getElementById('progress-bar'), modal: document.getElementById('level-complete-modal'), modalContent: document.getElementById('modal-content'), nextLevelBtn: document.getElementById('next-level-button'), };
    const state = { gridMatrix: [], isSelecting: false, selection: [], foundWords: new Set(), timeLeft: config.timeLeftInitial, timerInterval: null };
    const startSelection = (event) => { if (!event.target.classList.contains('grid-cell')) return; state.isSelecting = true; state.selection = [event.target]; event.target.classList.add('selection-highlight'); document.addEventListener('mouseover', moveSelection); document.addEventListener('mouseup', endSelection); };
    const moveSelection = (event) => { if (!state.isSelecting || !event.target.classList.contains('grid-cell')) return; if (!state.selection.includes(event.target)) { state.selection.push(event.target); event.target.classList.add('selection-highlight'); } };
    const endSelection = () => { if (!state.isSelecting) return; state.isSelecting = false; const selectedWord = state.selection.map(cell => cell.textContent).join(''); const reversedWord = [...selectedWord].reverse().join(''); checkWord(selectedWord, state.selection); checkWord(reversedWord, [...state.selection].reverse()); setTimeout(() => { state.selection.forEach(cell => cell.classList.remove('selection-highlight')); state.selection = []; }, 300); document.removeEventListener('mouseover', moveSelection); document.removeEventListener('mouseup', endSelection); };
    const checkWord = async (word, cells) => { if (!config.words.includes(word) || state.foundWords.has(word)) return; state.foundWords.add(word); updateUIAfterFoundWord(word, cells); updateProgressBar(); const response = await fetch(config.validateUrl, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrfToken }, body: JSON.stringify({ selected_word: word, level_id: config.levelId, time_left: state.timeLeft }) }); const data = await response.json(); if (data.success && data.found) { ui.score.textContent = data.score; if (data.level_completed) { clearInterval(state.timerInterval); showLevelCompleteModal(data.next_level_id); } } };
    const updateUIAfterFoundWord = (word, cells) => { cells.forEach(cell => { cell.classList.remove('selection-highlight'); cell.classList.add('found-word'); }); const chip = document.querySelector(`.word-chip[data-word="${word}"]`); if (chip) { chip.className = 'word-chip flex items-center bg-green-600/50 text-gray-400 font-semibold py-2 px-4 rounded-lg line-through'; chip.innerHTML = `<svg class="w-5 h-5 mr-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg><span>${word}</span>`; } };
    const updateProgressBar = () => { ui.progressBar.style.width = `${(state.foundWords.size / config.words.length) * 100}%`; };
    const showLevelCompleteModal = (nextLevelId) => { ui.modal.classList.remove('pointer-events-none', 'opacity-0'); ui.modalContent.classList.remove('scale-95'); if (nextLevelId) { ui.nextLevelBtn.onclick = () => window.location.href = `/game/play/${nextLevelId}`; } else { ui.nextLevelBtn.textContent = "Ver Pontuação Final"; ui.nextLevelBtn.onclick = () => window.location.href = config.endGameUrl; } };
    init();
});
</script>
@endsection
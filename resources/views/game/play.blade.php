@extends('layouts.app')

@section('content')

<style>
    /* Estilos (Mantidos) */
    .vocab-card { width: 160px; height: 100px; border-radius: 1rem; display: flex; align-items: center; justify-content: center; text-align: center; padding: 1rem; transition: background-color 0.5s ease; cursor: default; }
    .vocab-card.initial-state { background-color: #facc15; border: 2px solid #f59e0b; }
    .vocab-card.found-state { background-color: #22c55e; border: 2px solid #16a34a; }
    /* Feedback de Seleção por Clique */
    .selection-highlight { 
        background-color: #8B5CF6 !important; 
        box-shadow: 0 0 5px #8B5CF6; 
    }
    .found-word { background-color: #16a34a !important; color: white !important; }
    .lost-word { background-color: #d32f2f !important; color: white !important; border: 2px solid #b71c1c; animation: pulse-red 1s infinite; }
    @keyframes pulse-red { 0% { opacity: 0.8; } 50% { opacity: 1; } 100% { opacity: 0.8; } }

    /* Estilos do Tabuleiro (Mantidos) */
    #word-search-grid-container { max-width: 500px; margin: 0 auto; background-color: var(--dark-card, #2d3748); padding: 1.5rem; border-radius: 1rem; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); width: 100%; }
    #word-search-grid { display: grid; gap: 2px; width: 100%; }
    .grid-cell { aspect-ratio: 1 / 1; display: flex; align-items: center; justify-content: center; font-size: clamp(0.8rem, 3vw, 1.4rem); font-weight: bold; background-color: #4A5568; color: white; border-radius: 4px; transition: background-color 0.1s; }
</style>

{{-- Modal de Nível Concluído --}}
<div id="level-complete-modal" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 transition-opacity duration-300 opacity-0 pointer-events-none">
    <div id="modal-content" class="bg-dark-card rounded-2xl shadow-2xl p-8 text-center transform transition-all scale-95">
        <h2 class="text-4xl font-extrabold text-primary-purple mb-4">Nível Concluído!</h2>
        <p class="text-white text-lg mb-6">Você encontrou todas as palavras!</p>
        <button id="next-level-button" class="w-full bg-primary-purple border-b-violet-800 btn-duolingo text-white font-bold py-3 px-8 text-xl rounded-xl">Próximo Nível</button>
    </div>
</div>

<div class="max-w-7xl mx-auto">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-4 px-2">
        <h1 class="text-3xl font-bold text-white">Nível {{ $level->level_number }}</h1>
        <button id="quit-button" class="bg-gray-600/50 hover:bg-gray-600 border-b-gray-800 btn-duolingo text-white font-semibold py-2 px-4 rounded-xl">Desistir</button>
    </div>

    {{-- Barra de Progresso --}}
    <div class="w-full bg-dark-card rounded-full h-4 mb-6 border-2 border-dark-border">
        <div id="progress-bar" class="bg-primary-purple h-full rounded-full transition-all duration-500" style="width: 0%"></div>
    </div>

    {{-- Layout Principal --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="md:col-span-2 flex justify-center">
            <div id="word-search-grid-container">
                <div id="word-search-grid" class="grid gap-1 select-none cursor-pointer">
                    {{-- Células serão injetadas via JS --}}
                </div>
            </div>
        </div>


        {{-- Barra Lateral de Informações --}}
        <div class="space-y-6">
            <div class="bg-dark-card p-4 rounded-2xl shadow-lg flex items-center justify-between">
                <span class="text-lg font-semibold text-gray-300">🏆 Pontuação</span>
                <span id="score-display" class="text-2xl font-bold text-white">{{ $gameSession->total_score }}</span>
            </div>
            <div class="bg-dark-card p-4 rounded-2xl shadow-lg flex items-center justify-between">
                <span class="text-lg font-semibold text-gray-300">⏳ Tempo</span>
                <span id="timer-display" class="text-2xl font-bold bg-red-600 text-white px-3 py-1 rounded-lg">{{ gmdate("i:s", $level->time_limit_seconds) }}</span>
            </div>
            <div class="bg-dark-card p-6 rounded-2xl shadow-lg">
                <h3 class="font-semibold text-white text-xl mb-4 text-center">Vocabulário</h3>
                <div id="word-list" class="flex flex-wrap gap-4 justify-center">
                    
                    @foreach($level->words as $word)
                        <div class="vocab-card initial-state" data-word="{{ strtoupper($word->word) }}">
                            <span class="word-english text-2xl font-bold text-gray-800 tracking-wider">
                                {{ strtoupper($word->word) }}
                            </span>
                            <div class="word-translation flex-col items-center justify-center hidden">
                                <span class="text-4xl">{{ $word->icon }}</span>
                                <span class="text-lg font-bold text-white tracking-wider mt-1">
                                    {{ $word->translation }}
                                </span>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- 1. CONFIGURAÇÃO E ELEMENTOS DA UI ---
    const config = {
        gridSize: {{ $level->grid_size }},
        wordList: @json($level->words->pluck('word')).map(w => w.toUpperCase()),
        levelId: {{ $level->id }},
        timeLeftInitial: {{ $level->time_limit_seconds }},
        validateUrl: "{{ route('game.validate') }}",
        endGameUrl: "{{ route('game.end') }}",
        csrfToken: "{{ csrf_token() }}"
    };

    const ui = {
        grid: document.getElementById('word-search-grid'),
        score: document.getElementById('score-display'),
        timer: document.getElementById('timer-display'),
        progressBar: document.getElementById('progress-bar'),
        modal: document.getElementById('level-complete-modal'),
        modalContent: document.getElementById('modal-content'),
        nextLevelBtn: document.getElementById('next-level-button'),
        quitButton: document.getElementById('quit-button') 
    };

    const state = {
        gridMatrix: [],
        selection: [],
        foundWords: new Set(),
        wordsToFindLocations: {},
        timeLeft: config.timeLeftInitial,
        timerInterval: null,
        clickTimeout: null 
    };
    
    // --- 1.1 Funções de Áudio ---
    // ATENÇÃO: Substitua os caminhos abaixo por URLs reais dos seus arquivos MP3!
    const sounds = {
        click: new Audio("{{ asset('assets/sounds/click.mp3') }}"),    
        correct: new Audio("{{ asset('assets/sounds/correct.mp3') }}"), 
        error: new Audio("{{ asset('assets/sounds/error.mp3') }}"),     
        levelUp: new Audio("{{ asset('assets/sounds/level_up.mp3') }}") 
    };

    const playSound = (type) => {
        if (sounds[type]) {
            sounds[type].currentTime = 0;
            sounds[type].play().catch(e => {
                 console.warn("Audio playback blocked:", e.message);
            }); 
        }
    };

    // --- 2. LÓGICA DE SELEÇÃO POR CLIQUE ---
    
    // NOVO: Adiciona listener para o botão Desistir
    ui.quitButton.addEventListener('click', (e) => {
        e.preventDefault();
        showRemainingWords();
        // Redireciona após um pequeno atraso para o usuário ver o feedback
        setTimeout(() => {
            window.location.href = `${config.endGameUrl}?status=quit&score=${ui.score.textContent}`;
        }, 3000); 
    });

    const getCellElement = (r, c) => {
        return ui.grid.querySelector(`[data-row="${r}"][data-col="${c}"]`);
    };

    const handleCellClick = (event) => {
        const cell = event.target.closest('.grid-cell');
        if (!cell || cell.classList.contains('found-word')) return;

        const isFirstClick = state.selection.length === 0;

        if (isFirstClick) {
            resetSelection(); 
            startClickTimer();
            selectCell(cell); 
            playSound('click'); // Som de clique
        } else {
            const lastCell = state.selection[state.selection.length - 1];
            
            if (isAdjacent(lastCell, cell)) {
                resetClickTimer();
                startClickTimer();
                selectCell(cell); 
                playSound('click'); // Som de clique
                
                checkIfWordIsComplete(); 
            } else {
                // Se não for adjacente: reseta (erro de sequência)
                resetSelection(); 
                resetClickTimer(); 
                playSound('error'); // Som de erro
                
                selectCell(cell); 
                startClickTimer();
            }
        }
    };
    
    // --- 2.1 Funções de Suporte ao Clique ---
    const selectCell = (cell) => {
        state.selection.push(cell);
        // CORRIGIDO: Aplica a classe de destaque imediatamente na célula clicada
        cell.classList.add('selection-highlight'); 
    };

    const resetSelection = () => {
        state.selection.forEach(cell => {
            cell.classList.remove('selection-highlight');
        });
        state.selection = [];
    };

    const startClickTimer = () => {
        state.clickTimeout = setTimeout(() => {
            resetSelection();
        }, 1000); 
    };

    const resetClickTimer = () => {
        clearTimeout(state.clickTimeout);
    };

    const isAdjacent = (cell1, cell2) => {
        const r1 = parseInt(cell1.dataset.row);
        const c1 = parseInt(cell1.dataset.col);
        const r2 = parseInt(cell2.dataset.row);
        const c2 = parseInt(cell2.dataset.col);
        
        const dR = Math.abs(r1 - r2);
        const dC = Math.abs(c1 - c2);

        return (dR <= 1 && dC <= 1) && (dR !== 0 || dC !== 0); 
    };

    const checkIfWordIsComplete = () => {
        if (state.selection.length < 2) return; 
        
        const selectedWord = state.selection.map(cell => cell.textContent).join('');
        const reversedWord = [...selectedWord].reverse().join('');
        
        if (config.wordList.includes(selectedWord) || config.wordList.includes(reversedWord)) {
            resetClickTimer(); 
            checkWord(selectedWord, state.selection); 
        }
    };
    
    // --- 3. LÓGICA DO JOGO ---
    const checkWord = async (word, cells) => {
        if (state.foundWords.has(word)) {
            resetSelection();
            return false;
        } 
        
        if (!config.wordList.includes(word)) {
            resetSelection();
            return false; 
        }

        const originalSelection = [...cells];
        
        state.foundWords.add(word);

        const response = await fetch(config.validateUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrfToken },
            body: JSON.stringify({ selected_word: word, level_id: config.levelId, time_left: state.timeLeft })
        });
        
        const data = await response.json();
        
        resetSelection(); 

        if (data.success && data.found) {
            playSound('correct'); // Som de acerto
            updateUIAfterFoundWord(word, originalSelection);
            updateProgressBar();
            ui.score.textContent = data.score;
            
            if(state.wordsToFindLocations[word]) delete state.wordsToFindLocations[word];

            if (data.level_completed) {
                playSound('levelUp'); // Som de passagem de fase
                clearInterval(state.timerInterval);
                showLevelCompleteModal(data.next_level_id);
            }
            return true;
        }
        
        state.foundWords.delete(word);
        playSound('error'); // Som de erro (caso o backend invalide)
        return false;
    };
    
    // NOVO: Função para marcar e mostrar as palavras que faltaram
    const showRemainingWords = () => {
        clearInterval(state.timerInterval); 
        
        for (const word in state.wordsToFindLocations) {
            const locations = state.wordsToFindLocations[word];
            locations.forEach(loc => {
                const cell = getCellElement(loc.r, loc.c);
                if (cell) {
                    cell.classList.add('lost-word');
                }
            });
            const card = document.querySelector(`.vocab-card[data-word="${word}"]`);
            if (card) {
                card.classList.remove('initial-state');
                card.classList.remove('found-state');
                card.classList.add('lost-word'); 
            }
        }
        ui.grid.removeEventListener('click', handleCellClick);
    };

    // --- 4. FUNÇÕES DE ATUALIZAÇÃO DA INTERFACE ---
    const updateUIAfterFoundWord = (word, cells) => {
        cells.forEach(cell => {
            cell.classList.remove('selection-highlight');
            cell.classList.add('found-word');
        });
        const card = document.querySelector(`.vocab-card[data-word="${word}"]`);
        if (card) {
            card.classList.remove('initial-state');
            card.classList.add('found-state');
            const englishText = card.querySelector('.word-english');
            const translationDiv = card.querySelector('.word-translation');
            if (englishText) englishText.classList.add('hidden');
            if (translationDiv) {
                translationDiv.classList.remove('hidden');
                translationDiv.classList.add('flex');
            }
        }
    };
    const updateProgressBar = () => { ui.progressBar.style.width = `${(state.foundWords.size / config.wordList.length) * 100}%`; };
    const showLevelCompleteModal = (nextLevelId) => {
        ui.modal.classList.remove('pointer-events-none', 'opacity-0');
        ui.modalContent.classList.remove('scale-95');
        if (nextLevelId) {
            ui.nextLevelBtn.onclick = () => window.location.href = `/game/play/${nextLevelId}`;
        } else {
            ui.nextLevelBtn.textContent = "Ver Pontuação Final";
            ui.nextLevelBtn.onclick = () => window.location.href = config.endGameUrl;
        }
    };

    // --- 5. INICIALIZAÇÃO E FUNÇÕES DO GRID ---
    const init = () => {
        state.gridMatrix = Array.from({ length: config.gridSize }, () => Array(config.gridSize).fill(''));
        const directions = [{ r: 0, c: 1 }, { r: 1, c: 0 }, { r: 1, c: 1 }, { r: 0, c: -1 }, { r: -1, c: 0 }, { r: -1, c: -1 }, { r: 1, c: -1 }, { r: -1, c: 1 }];
        const wordListToPlace = config.wordList.map(word => word);
        state.wordsToFindLocations = {}; 

        // Algoritmo de posicionamento de palavras
        wordListToPlace.forEach(word => {
            let placed = false; 
            let attempts = 0; 
            const maxAttempts = 100;
            while(!placed && attempts < maxAttempts) { 
                attempts++; 
                const dir = directions[Math.floor(Math.random() * directions.length)];
                const rStart = Math.floor(Math.random() * config.gridSize);
                const cStart = Math.floor(Math.random() * config.gridSize); 
                let canPlace = true; 
                let locations = [];

                // 1. Checagem
                for (let i = 0; i < word.length; i++) { 
                    const r = rStart + i * dir.r;
                    const c = cStart + i * dir.c;
                    
                    if (r < 0 || r >= config.gridSize || c < 0 || c >= config.gridSize) {
                        canPlace = false; break;
                    }
                    if (state.gridMatrix[r][c] !== '' && state.gridMatrix[r][c] !== word[i]) { 
                        canPlace = false; break;
                    } 
                    locations.push({r, c});
                } 
                
                // 2. Colocação
                if (canPlace) { 
                    state.wordsToFindLocations[word] = locations; 
                    for (let i = 0; i < word.length; i++) { 
                        state.gridMatrix[rStart + i * dir.r][cStart + i * dir.c] = word[i]; 
                    } 
                    placed = true; 
                } 
            } 
        });
        
        // Preenchimento com letras aleatórias (mantido)
        const alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        for (let r = 0; r < config.gridSize; r++) { 
            for (let c = 0; c < config.gridSize; c++) { 
                if (state.gridMatrix[r][c] === '') { 
                    state.gridMatrix[r][c] = alphabet[Math.floor(Math.random() * alphabet.length)]; 
                } 
            } 
        }
        
        // Define o grid-template-columns e renderiza as células com data-attributes (corrigido)
        ui.grid.style.gridTemplateColumns = `repeat(${config.gridSize}, 1fr)`;
        
        ui.grid.innerHTML = state.gridMatrix.map((row, rIdx) => row.map((letter, cIdx) => `<div class="grid-cell" data-row="${rIdx}" data-col="${cIdx}">${letter}</div>`).join('')).join('');
        
        // Adiciona listener para o modo de clique sequencial
        ui.grid.addEventListener('click', handleCellClick);
        
        // Inicialização do Timer
        state.timerInterval = setInterval(() => {
            state.timeLeft--;
            if (state.timeLeft >= 0) {
                ui.timer.textContent = new Date(state.timeLeft * 1000).toISOString().substr(14, 5);
                if (state.timeLeft <= 10) {
                     ui.timer.classList.add('bg-red-800');
                } else {
                     ui.timer.classList.remove('bg-red-800');
                }
            } else {
                clearInterval(state.timerInterval);
                showRemainingWords(); 
                // Redireciona após um atraso para que o usuário veja o feedback
                setTimeout(() => {
                    window.location.href = `${config.endGameUrl}?status=failed`;
                }, 3000); 
            }
        }, 1000);
    };

    init();
});
</script>
@endsection
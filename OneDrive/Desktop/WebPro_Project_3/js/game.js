/**
 * Main Game Controller
 * Orchestrates all game functionality
 */

let currentPuzzle = null;
let currentPuzzleData = null;
let hintsUsed = 0;
let powerupsUsed = 0;

/**
 * Initialize game on page load
 */
async function initializeGame() {
    // Load initial data
    await loadGameData();
    
    // Start new game
    await startNewGame();
    
    // Setup event listeners
    setupEventListeners();
}

/**
 * Load user game data
 */
async function loadGameData() {
    try {
        const [stats, powerups, hints, story] = await Promise.all([
            apiRequest('get_stats'),
            apiRequest('get_powerups'),
            apiRequest('get_daily_hints'),
            apiRequest('get_story_progress')
        ]);
        
        if (stats.success) {
            updateSessionStats(stats.stats);
        }
        
        if (powerups.success) {
            updatePowerupCounts(powerups.powerups);
        }
        
        if (hints.success) {
            document.getElementById('hintsRemaining').textContent = hints.hints_remaining;
        }
        
        if (story.success) {
            updateStoryProgress(story);
        }
        
    } catch (error) {
        console.error('Error loading game data:', error);
    }
}

/**
 * Start new game
 */
async function startNewGame() {
    showLoading('Generating puzzle...');
    
    const result = await apiRequest('generate_puzzle', { size: 4 });
    
    hideLoading();
    
    if (!result.success) {
        showMessage('Failed to generate puzzle: ' + result.error, 'error');
        return;
    }
    
    currentPuzzleData = result.puzzle;
    hintsUsed = 0;
    powerupsUsed = 0;
    
    // Update difficulty display
    document.getElementById('difficulty').textContent = result.puzzle.difficulty;
    document.getElementById('score').textContent = '0';
    
    // Initialize puzzle
    currentPuzzle = new Puzzle(result.puzzle.size);
    currentPuzzle.init(result.puzzle.initial_state, result.puzzle.solution_state);
    
    // Render puzzle
    const board = document.getElementById('puzzleBoard');
    currentPuzzle.render(board);
    
    // Add tile click listeners
    board.querySelectorAll('.puzzle-tile').forEach(tile => {
        tile.addEventListener('click', handleTileClick);
    });
    
    showMessage('New puzzle loaded! Good luck! 🎄', 'info');
}

/**
 * Handle tile click
 */
async function handleTileClick(event) {
    if (currentPuzzle.solved) return;
    
    const tile = event.currentTarget;
    const position = parseInt(tile.dataset.position);
    
    if (!currentPuzzle.isMovable(position)) {
        return;
    }
    
    // Play sound
    playSound('tileClick');
    
    // Move tile
    const result = currentPuzzle.moveTile(position);
    
    // Re-render
    const board = document.getElementById('puzzleBoard');
    currentPuzzle.render(board);
    
    // Re-attach listeners
    board.querySelectorAll('.puzzle-tile').forEach(t => {
        t.addEventListener('click', handleTileClick);
    });
    
    // Check if solved
    if (result === 'solved') {
        await handlePuzzleComplete();
    }
}

/**
 * Handle puzzle completion
 */
async function handlePuzzleComplete() {
    playSound('victory');
    stopMusic();
    
    const timeSeconds = currentPuzzle.getElapsedTime();
    const moves = currentPuzzle.moves;
    
    showLoading('Saving progress...');
    
    const result = await apiRequest('save_session', {
        puzzle_id: currentPuzzleData.puzzle_id,
        moves: moves,
        time_seconds: timeSeconds,
        difficulty: currentPuzzleData.difficulty,
        puzzle_size: currentPuzzleData.size,
        completed: true,
        hints_used: hintsUsed,
        powerups_used: powerupsUsed
    });
    
    hideLoading();
    
    if (result.success) {
        // Update session stats
        if (result.user_data) {
            updateSessionStats(result.user_data);
        }
        
        // Show victory modal
        showVictoryModal(moves, timeSeconds, result.score, result.new_achievements, result.new_story_chapters);
        
        // Trigger confetti
        createConfetti();
    } else {
        showMessage('Failed to save progress', 'error');
    }
}

/**
 * Show victory modal
 */
function showVictoryModal(moves, time, score, achievements, storyChapters) {
    document.getElementById('victoryMoves').textContent = moves;
    document.getElementById('victoryTime').textContent = formatTime(time);
    document.getElementById('victoryScore').textContent = score;
    
    // Show achievements if any
    if (achievements && achievements.length > 0) {
        const achievementsSection = document.getElementById('victoryAchievements');
        const achievementsList = document.getElementById('newAchievementsList');
        achievementsList.innerHTML = '';
        
        achievements.forEach(ach => {
            const div = document.createElement('div');
            div.className = 'achievement-item';
            div.innerHTML = `
                <span class="achievement-icon">🏆</span>
                <span class="achievement-name">${ach.achievement_type.replace('_', ' ')}</span>
            `;
            achievementsList.appendChild(div);
        });
        
        achievementsSection.classList.remove('hidden');
    }
    
    // Show story chapters if any
    if (storyChapters && storyChapters.length > 0) {
        const storySection = document.getElementById('victoryStory');
        const storyContent = document.getElementById('newStoryContent');
        
        const chapter = storyChapters[0]; // Show first unlocked chapter
        storyContent.innerHTML = `
            <h4>Chapter ${chapter.chapter_number}: ${chapter.title}</h4>
            <p>${chapter.content}</p>
        `;
        
        storySection.classList.remove('hidden');
        
        // Mark as viewed
        apiRequest('mark_chapter_viewed', { chapter_number: chapter.chapter_number });
    }
    
    showModal('victoryModal');
    // Add confetti effect to victory modal
    const victoryModal = document.getElementById('victoryModal');
    if (victoryModal) {
        victoryModal.classList.add('confetti');
        setTimeout(() => victoryModal.classList.remove('confetti'), 3000);
    }
}

/**
 * Setup event listeners
 */
function setupEventListeners() {
    // New game button
    const newGameBtn = document.getElementById('newGameBtn');
    if (newGameBtn) newGameBtn.addEventListener('click', startNewGame);
    
    // Play again button
    const playAgainBtn = document.getElementById('playAgainBtn');
    if (playAgainBtn) playAgainBtn.addEventListener('click', () => {
        hideModal('victoryModal');
        startNewGame();
    });
    
    // View profile button
    const viewProfileBtn = document.getElementById('viewProfileBtn');
    if (viewProfileBtn) viewProfileBtn.addEventListener('click', () => {
        window.location.href = 'profile.html';
    });
    
    // Hint button
    const useHintBtn = document.getElementById('useHintBtn');
    if (useHintBtn) useHintBtn.addEventListener('click', useHint);
    
    // Power-up buttons
    document.querySelectorAll('.btn-powerup').forEach(btn => {
        btn.addEventListener('click', () => {
            const powerupType = btn.dataset.powerup;
            usePowerup(powerupType);
        });
    });
    
    // Theme button
    const themeBtn = document.getElementById('themeBtn');
    if (themeBtn) themeBtn.addEventListener('click', () => {
        showModal('themeModal');
    });
    
    // Theme selection
    document.querySelectorAll('.theme-option').forEach(option => {
        option.addEventListener('click', async () => {
            const theme = option.dataset.theme;
            const result = await apiRequest('update_theme', { theme });
            if (result.success) {
                document.body.setAttribute('data-theme', theme);
                hideModal('themeModal');
                showMessage(`Theme changed to ${theme}!`, 'success');
            }
        });
    });
    
    // Preview button
    const previewBtn = document.getElementById('previewBtn');
    if (previewBtn) previewBtn.addEventListener('click', showPreview);
    
    // Story button
    const viewStoryBtn = document.getElementById('viewStoryBtn');
    if (viewStoryBtn) viewStoryBtn.addEventListener('click', showStoryModal);
}

/**
 * Use hint
 */
async function useHint() {
    if (currentPuzzle.solved) return;
    
    const result = await apiRequest('get_hint', {
        current_state: currentPuzzle.tiles,
        size: currentPuzzle.size
    });
    
    if (!result.success) {
        showMessage(result.error, 'error');
        return;
    }
    
    // Highlight the hint tile with sparkle animation and sound
    const hintPosition = result.hint;
    const tile = document.querySelector(`[data-position="${hintPosition}"]`);
    if (tile) {
        tile.classList.add('hint-highlight', 'sparkle-anim');
        playSound('hint');
        setTimeout(() => {
            tile.classList.remove('hint-highlight', 'sparkle-anim');
        }, 2000);
    }
    
    hintsUsed++;
    document.getElementById('hintsRemaining').textContent = result.hints_remaining;
    showMessage('Hint: Move the highlighted tile!', 'info');
}

/**
 * Use power-up
 */
async function usePowerup(type) {
    if (currentPuzzle.solved) return;
    
    const result = await apiRequest('use_powerup', { powerup_type: type });
    
    if (!result.success) {
        showMessage(result.error, 'error');
        return;
    }
    
    powerupsUsed++;
    playSound('powerup');
    // Apply power-up effect with pulse animation
    if (type === 'reveal_move') {
        // Same as hint
        const validMoves = currentPuzzle.getValidMoves();
        if (validMoves.length > 0) {
            const bestMove = validMoves[0];
            const tile = document.querySelector(`[data-position="${bestMove}"]`);
            if (tile) {
                tile.classList.add('hint-highlight', 'pulse-anim');
                setTimeout(() => tile.classList.remove('hint-highlight', 'pulse-anim'), 2000);
            }
        }
    } else if (type === 'swap_tiles') {
        showMessage('Swap tiles power-up activated!', 'success');
    } else if (type === 'freeze_timer') {
        currentPuzzle.stopTimer();
        showMessage('Timer frozen for 10 seconds!', 'success');
        setTimeout(() => {
            if (!currentPuzzle.solved) {
                currentPuzzle.startTimer();
            }
        }, 10000);
    }
    
    // Update count
    updatePowerupCounts({ [type]: result.remaining });
}

/**
 * Update powerup counts
 */
function updatePowerupCounts(powerups) {
    if (powerups.reveal_move !== undefined) {
        document.getElementById('powerupRevealCount').textContent = powerups.reveal_move;
    }
    if (powerups.swap_tiles !== undefined) {
        document.getElementById('powerupSwapCount').textContent = powerups.swap_tiles;
    }
    if (powerups.freeze_timer !== undefined) {
        document.getElementById('powerupFreezeCount').textContent = powerups.freeze_timer;
    }
}

/**
 * Update session stats
 */
function updateSessionStats(stats) {
    const winsEl = document.getElementById('sessionWins');
    const streakEl = document.getElementById('currentStreak');
    const timeEl = document.getElementById('bestTime');
    
    if (winsEl) winsEl.textContent = stats.total_wins || 0;
    if (streakEl) streakEl.textContent = stats.current_streak || 0;
    if (timeEl) timeEl.textContent = stats.best_time ? formatTime(stats.best_time) : '--';
}

/**
 * Update story progress
 */
function updateStoryProgress(data) {
    const unlockedChapters = data.progress.filter(p => p.unlocked).length;
        const currentChapter = document.getElementById('currentChapter');
        if (currentChapter) currentChapter.textContent = unlockedChapters;
    
    const percentage = (unlockedChapters / 7) * 100;
        const progressBar = document.getElementById('storyProgressBar');
        if (progressBar) progressBar.style.width = `${percentage}%`;
}

/**
 * Show preview
 */
function showPreview() {
    const previewBoard = document.getElementById('previewBoard');
    const tempPuzzle = new Puzzle(currentPuzzle.size);
    tempPuzzle.tiles = [...currentPuzzle.solvedState];
    tempPuzzle.emptyPos = tempPuzzle.tiles.indexOf(0);
    tempPuzzle.render(previewBoard);
    showModal('previewModal');
}

/**
 * Show story modal
 */
async function showStoryModal() {
    const result = await apiRequest('get_story_progress');
    if (!result.success) return;
    
    const container = document.getElementById('storyChaptersList');
    container.innerHTML = '';
    
    result.all_chapters.forEach(chapter => {
        const progress = result.progress.find(p => p.chapter_number === chapter.chapter_number);
        const unlocked = progress && progress.unlocked;
        
        const div = document.createElement('div');
        div.className = `story-chapter ${unlocked ? 'unlocked' : 'locked'}`;
        div.innerHTML = `
            <h3>Chapter ${chapter.chapter_number}: ${chapter.title}</h3>
            ${unlocked ? `<p>${chapter.content}</p>` : `<p class="locked-message">🔒 Unlock after ${chapter.unlock_requirement} wins</p>`}
        `;
        container.appendChild(div);
    });
    
    showModal('storyModal');
}

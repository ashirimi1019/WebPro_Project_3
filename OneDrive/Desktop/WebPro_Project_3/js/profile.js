/**
 * Christmas Fifteen Puzzle - Profile Page
 * Handles profile display, statistics, achievements, and game history
 */

// ==========================================
// INITIALIZATION
// ==========================================

document.addEventListener('DOMContentLoaded', async () => {
    try {
        // Check authentication
        const authCheck = await checkAuthentication();
        if (!authCheck.authenticated) {
            window.location.href = 'login.html';
            return;
        }

        // Load all profile data
        await Promise.all([
            loadProfile(),
            loadStats(),
            loadAchievements(),
            loadGameHistory(),
            loadLeaderboard()
        ]);

        // Initialize event listeners
        initializeEventListeners();

        // Add snowfall effect
        createSnowfall();
    } catch (error) {
        console.error('Profile initialization error:', error);
        showMessage('Failed to load profile data', 'error');
    }
});

// ==========================================
// PROFILE DATA
// ==========================================

async function loadProfile() {
    try {
        const response = await apiRequest('get_profile', {});
        
        if (response.success && response.profile) {
            displayProfile(response.profile);
        } else {
            throw new Error(response.message || 'Failed to load profile');
        }
    } catch (error) {
        console.error('Load profile error:', error);
        showMessage('Failed to load profile', 'error');
    }
}

function displayProfile(profile) {
    // Profile header
    const usernameEl = document.getElementById('profileUsername');
    const emailEl = document.getElementById('profileEmail');
    const joinDateEl = document.getElementById('joinDate');
    const themeEl = document.getElementById('currentTheme');

    if (usernameEl) usernameEl.textContent = profile.username || 'Anonymous';
    if (emailEl) emailEl.textContent = profile.email || 'No email';
    if (joinDateEl) joinDateEl.textContent = formatDate(profile.created_at);
    if (themeEl) themeEl.textContent = capitalizeFirst(profile.theme || 'workshop');
}

// ==========================================
// STATISTICS
// ==========================================

async function loadStats() {
    try {
        const response = await apiRequest('get_stats', {});
        
        if (response.success && response.stats) {
            displayStats(response.stats);
        }
    } catch (error) {
        console.error('Load stats error:', error);
    }
}

function displayStats(stats) {
    // Overview stats
    setTextContent('totalGames', stats.total_games || 0);
    setTextContent('gamesWon', stats.games_won || 0);
    setTextContent('winRate', formatPercentage(stats.win_rate));
    setTextContent('totalMoves', stats.total_moves || 0);
    setTextContent('avgMoves', Math.round(stats.avg_moves || 0));
    setTextContent('bestMoves', stats.best_moves || '-');
    setTextContent('totalTime', formatTime(stats.total_time || 0));
    setTextContent('avgTime', formatTime(stats.avg_time || 0));
    setTextContent('bestTime', stats.best_time ? formatTime(stats.best_time) : '-');
    setTextContent('totalScore', stats.total_score || 0);
    setTextContent('avgScore', Math.round(stats.avg_score || 0));
    setTextContent('bestScore', stats.best_score || '-');
    setTextContent('currentStreak', stats.current_streak || 0);
    setTextContent('longestStreak', stats.longest_streak || 0);
    setTextContent('hintsUsed', stats.hints_used || 0);
    setTextContent('powerupsUsed', stats.powerups_used || 0);

    // Difficulty breakdown
    setTextContent('easyGames', stats.easy_games || 0);
    setTextContent('mediumGames', stats.medium_games || 0);
    setTextContent('hardGames', stats.hard_games || 0);
    setTextContent('expertGames', stats.expert_games || 0);
}

// ==========================================
// ACHIEVEMENTS
// ==========================================

async function loadAchievements() {
    try {
        const response = await apiRequest('get_achievements', {});
        
        if (response.success && response.achievements) {
            displayAchievements(response.achievements);
        }
    } catch (error) {
        console.error('Load achievements error:', error);
    }
}

function displayAchievements(achievements) {
    const grid = document.getElementById('achievementsGrid');
    if (!grid) return;

    grid.innerHTML = '';

    // Achievement definitions
    const achievementDefs = [
        { id: 'first_win', icon: '🎉', name: 'First Victory', desc: 'Complete your first puzzle' },
        { id: 'speed_demon', icon: '⚡', name: 'Speed Demon', desc: 'Complete a puzzle in under 60 seconds' },
        { id: 'efficient', icon: '🎯', name: 'Efficient Solver', desc: 'Complete puzzle with minimal moves' },
        { id: 'streak_5', icon: '🔥', name: 'On Fire', desc: 'Win 5 games in a row' },
        { id: 'streak_10', icon: '🌟', name: 'Unstoppable', desc: 'Win 10 games in a row' },
        { id: 'master', icon: '👑', name: 'Puzzle Master', desc: 'Complete 100 puzzles' },
        { id: 'perfectionist', icon: '💎', name: 'Perfectionist', desc: 'Get perfect score 10 times' },
        { id: 'story_complete', icon: '📖', name: 'Story Keeper', desc: 'Complete all story chapters' }
    ];

    achievementDefs.forEach(def => {
        const achievement = achievements.find(a => a.achievement_type === def.id);
        const unlocked = achievement && achievement.unlocked_at;

        const badge = document.createElement('div');
        badge.className = `achievement-badge ${unlocked ? 'unlocked' : 'locked'}`;
        badge.innerHTML = `
            <span class="achievement-icon">${def.icon}</span>
            <div class="achievement-tooltip">
                <div class="achievement-name">${def.name}</div>
                <div class="achievement-desc">${def.desc}</div>
                ${unlocked ? `<div class="achievement-date">Unlocked: ${formatDate(achievement.unlocked_at)}</div>` : '<div class="achievement-locked">🔒 Locked</div>'}
            </div>
        `;

        if (unlocked) {
            badge.addEventListener('click', () => {
                showMessage(`${def.icon} ${def.name}: ${def.desc}`, 'success');
            });
        }

        grid.appendChild(badge);
    });

    // Update achievement count
    const unlockedCount = achievements.filter(a => a.unlocked_at).length;
    setTextContent('achievementCount', `${unlockedCount}/${achievementDefs.length}`);
}

// ==========================================
// GAME HISTORY
// ==========================================

async function loadGameHistory() {
    try {
        const response = await apiRequest('get_game_history', { limit: 20 });
        
        if (response.success && response.games) {
            displayGameHistory(response.games);
        }
    } catch (error) {
        console.error('Load game history error:', error);
    }
}

function displayGameHistory(games) {
    const tbody = document.getElementById('gameHistoryBody');
    if (!tbody) return;

    tbody.innerHTML = '';

    if (games.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No games played yet</td></tr>';
        return;
    }

    games.forEach((game, index) => {
        const row = document.createElement('tr');
        row.className = 'stagger-item';
        row.style.animationDelay = `${index * 0.05}s`;
        
        const statusClass = game.completed ? 'success' : 'secondary';
        const statusIcon = game.completed ? '✓' : '○';
        
        row.innerHTML = `
            <td>${formatDate(game.created_at)}</td>
            <td><span class="badge badge-${game.difficulty}">${capitalizeFirst(game.difficulty)}</span></td>
            <td>${game.moves || 0}</td>
            <td>${game.time_elapsed ? formatTime(game.time_elapsed) : '-'}</td>
            <td>${game.score || 0}</td>
            <td><span class="badge badge-${statusClass}">${statusIcon} ${game.completed ? 'Won' : 'In Progress'}</span></td>
        `;
        
        tbody.appendChild(row);
    });
}

// ==========================================
// LEADERBOARD
// ==========================================

async function loadLeaderboard() {
    try {
        const response = await apiRequest('get_leaderboard', { limit: 10 });
        
        if (response.success && response.leaderboard) {
            displayLeaderboard(response.leaderboard);
        }
    } catch (error) {
        console.error('Load leaderboard error:', error);
    }
}

function displayLeaderboard(leaderboard) {
    const tbody = document.getElementById('leaderboardBody');
    if (!tbody) return;

    tbody.innerHTML = '';

    if (leaderboard.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">No leaderboard data yet</td></tr>';
        return;
    }

    leaderboard.forEach((entry, index) => {
        const row = document.createElement('tr');
        row.className = 'stagger-item';
        row.style.animationDelay = `${index * 0.05}s`;
        
        // Highlight current user
        if (entry.is_current_user) {
            row.classList.add('highlight');
        }
        
        // Rank medals
        let rankDisplay = `#${entry.rank}`;
        if (entry.rank === 1) rankDisplay = '🥇';
        else if (entry.rank === 2) rankDisplay = '🥈';
        else if (entry.rank === 3) rankDisplay = '🥉';
        
        row.innerHTML = `
            <td class="rank">${rankDisplay}</td>
            <td>${entry.username}</td>
            <td>${entry.total_score || 0}</td>
            <td>${entry.games_won || 0}</td>
        `;
        
        tbody.appendChild(row);
    });
}

// ==========================================
// EVENT LISTENERS
// ==========================================

function initializeEventListeners() {
    // Theme change button
    const themeBtn = document.getElementById('changeThemeBtn');
    if (themeBtn) {
        themeBtn.addEventListener('click', () => {
            const modal = document.getElementById('themeModal');
            if (modal) showModal('themeModal');
        });
    }

    // Theme selection
    const themeButtons = document.querySelectorAll('[data-theme-select]');
    themeButtons.forEach(btn => {
        btn.addEventListener('click', async () => {
            const theme = btn.dataset.themeSelect;
            await changeTheme(theme);
        });
    });

    // Refresh buttons
    const refreshStatsBtn = document.getElementById('refreshStats');
    if (refreshStatsBtn) {
        refreshStatsBtn.addEventListener('click', async () => {
            await loadStats();
            showMessage('Stats refreshed!', 'success');
        });
    }

    const refreshHistoryBtn = document.getElementById('refreshHistory');
    if (refreshHistoryBtn) {
        refreshHistoryBtn.addEventListener('click', async () => {
            await loadGameHistory();
            showMessage('History refreshed!', 'success');
        });
    }

    // Back to game button
    const backBtn = document.getElementById('backToGame');
    if (backBtn) {
        backBtn.addEventListener('click', () => {
            window.location.href = 'index.html';
        });
    }
}

// ==========================================
// THEME MANAGEMENT
// ==========================================

async function changeTheme(theme) {
    try {
        const response = await apiRequest('update_theme', { theme });
        
        if (response.success) {
            document.documentElement.setAttribute('data-theme', theme);
            showMessage(`Theme changed to ${capitalizeFirst(theme)}!`, 'success');
            hideModal('themeModal');
            
            // Update display
            setTextContent('currentTheme', capitalizeFirst(theme));
        } else {
            throw new Error(response.message || 'Failed to change theme');
        }
    } catch (error) {
        console.error('Change theme error:', error);
        showMessage('Failed to change theme', 'error');
    }
}

// ==========================================
// UTILITY FUNCTIONS
// ==========================================

function setTextContent(id, value) {
    const element = document.getElementById(id);
    if (element) {
        element.textContent = value;
    }
}

function formatPercentage(value) {
    if (value === null || value === undefined) return '0%';
    return `${Math.round(value)}%`;
}

function formatDate(dateString) {
    if (!dateString) return 'Unknown';
    
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 0) return 'Today';
    if (diffDays === 1) return 'Yesterday';
    if (diffDays < 7) return `${diffDays} days ago`;
    if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`;
    if (diffDays < 365) return `${Math.floor(diffDays / 30)} months ago`;
    
    return date.toLocaleDateString();
}

function capitalizeFirst(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}

// ==========================================
// LOGOUT
// ==========================================

async function handleLogout() {
    try {
        await apiRequest('logout', {});
        window.location.href = 'login.html';
    } catch (error) {
        console.error('Logout error:', error);
        // Still redirect even if logout fails
        window.location.href = 'login.html';
    }
}

// Add logout button listener if exists
const logoutBtn = document.getElementById('logoutBtn');
if (logoutBtn) {
    logoutBtn.addEventListener('click', handleLogout);
}

/**
 * Power-ups Module
 * Manages power-up functionality and effects
 */

class PowerupManager {
    constructor() {
        this.inventory = {
            reveal_move: 0,
            swap_tiles: 0,
            freeze_timer: 0
        };
    }
    
    /**
     * Load power-ups from server
     */
    async loadInventory() {
        const result = await apiRequest('get_powerups');
        if (result.success) {
            this.inventory = result.powerups;
            this.updateUI();
        }
    }
    
    /**
     * Use power-up
     */
    async use(type, puzzle) {
        if (!this.inventory[type] || this.inventory[type] <= 0) {
            showMessage('No power-ups of this type available!', 'error');
            return false;
        }
        
        const result = await apiRequest('use_powerup', { powerup_type: type });
        
        if (!result.success) {
            showMessage(result.error, 'error');
            return false;
        }
        
        this.inventory[type] = result.remaining;
        this.updateUI();
        playSound('powerup');
        
        // Apply effect
        switch(type) {
            case 'reveal_move':
                this.applyRevealMove(puzzle);
                break;
            case 'swap_tiles':
                this.applySwapTiles(puzzle);
                break;
            case 'freeze_timer':
                this.applyFreezeTimer(puzzle);
                break;
        }
        
        return true;
    }
    
    /**
     * Reveal best move
     */
    applyRevealMove(puzzle) {
        const validMoves = puzzle.getValidMoves();
        if (validMoves.length === 0) return;
        
        // Highlight best move
        const bestMove = validMoves[0];
        const tile = document.querySelector(`[data-position="${bestMove}"]`);
        if (tile) {
            tile.classList.add('powerup-highlight');
            glowElement(tile, 3000);
            setTimeout(() => {
                tile.classList.remove('powerup-highlight');
            }, 3000);
        }
        
        showMessage('✨ Best move revealed!', 'success');
    }
    
    /**
     * Swap two tiles
     */
    applySwapTiles(puzzle) {
        // Find two tiles that are out of place
        const wrongPositions = [];
        for (let i = 0; i < puzzle.tiles.length; i++) {
            if (puzzle.tiles[i] !== 0 && puzzle.tiles[i] !== puzzle.solvedState[i]) {
                wrongPositions.push(i);
            }
        }
        
        if (wrongPositions.length >= 2) {
            const pos1 = wrongPositions[0];
            const pos2 = wrongPositions[1];
            
            // Swap tiles
            [puzzle.tiles[pos1], puzzle.tiles[pos2]] = [puzzle.tiles[pos2], puzzle.tiles[pos1]];
            
            // Re-render
            const board = document.getElementById('puzzleBoard');
            puzzle.render(board);
            
            showMessage('🔄 Two tiles swapped!', 'success');
        }
    }
    
    /**
     * Freeze timer
     */
    applyFreezeTimer(puzzle) {
        puzzle.stopTimer();
        showMessage('❄️ Timer frozen for 10 seconds!', 'success');
        
        setTimeout(() => {
            if (!puzzle.solved) {
                puzzle.startTimer();
                showMessage('⏰ Timer resumed!', 'info');
            }
        }, 10000);
    }
    
    /**
     * Update UI
     */
    updateUI() {
        document.getElementById('powerupRevealCount').textContent = this.inventory.reveal_move;
        document.getElementById('powerupSwapCount').textContent = this.inventory.swap_tiles;
        document.getElementById('powerupFreezeCount').textContent = this.inventory.freeze_timer;
    }
    
    /**
     * Earn power-up
     */
    earn(type, quantity = 1) {
        if (this.inventory[type] !== undefined) {
            this.inventory[type] += quantity;
            this.updateUI();
            showMessage(`🎁 Earned ${quantity} ${type.replace('_', ' ')}!`, 'success');
        }
    }
}

// Export for use in game.js
window.PowerupManager = PowerupManager;

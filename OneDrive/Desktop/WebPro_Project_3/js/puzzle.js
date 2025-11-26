/**
 * Puzzle Logic Module
 * Handles puzzle generation, tile movement, and win detection
 */

class Puzzle {
    constructor(size = 4) {
        this.size = size;
        this.tiles = [];
        this.emptyPos = 0;
        this.solvedState = [];
        this.moves = 0;
        this.startTime = null;
        this.timerInterval = null;
        this.solved = false;
    }
    
    /**
     * Initialize puzzle with given state
     */
    init(initialState, solvedState) {
        this.tiles = [...initialState];
        this.solvedState = [...solvedState];
        this.emptyPos = this.tiles.indexOf(0);
        this.moves = 0;
        this.solved = false;
        this.startTime = Date.now();
        this.startTimer();
    }
    
    /**
     * Start game timer
     */
    startTimer() {
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
        }
        
        this.timerInterval = setInterval(() => {
            if (!this.solved) {
                const elapsed = Math.floor((Date.now() - this.startTime) / 1000);
                document.getElementById('timer').textContent = formatTime(elapsed);
            }
        }, 1000);
    }
    
    /**
     * Stop timer
     */
    stopTimer() {
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
            this.timerInterval = null;
        }
    }
    
    /**
     * Get elapsed time in seconds
     */
    getElapsedTime() {
        return Math.floor((Date.now() - this.startTime) / 1000);
    }
    
    /**
     * Check if position is valid and movable
     */
    isMovable(position) {
        const row = Math.floor(position / this.size);
        const col = position % this.size;
        const emptyRow = Math.floor(this.emptyPos / this.size);
        const emptyCol = this.emptyPos % this.size;
        
        // Check if tile is adjacent to empty tile
        return (
            (row === emptyRow && Math.abs(col - emptyCol) === 1) ||
            (col === emptyCol && Math.abs(row - emptyRow) === 1)
        );
    }
    
    /**
     * Move tile at given position
     */
    moveTile(position) {
        if (!this.isMovable(position)) {
            return false;
        }
        // Animate moved tile
        const board = document.getElementById('puzzleBoard');
        if (board && board.children[position]) {
            board.children[position].classList.add('move-animate');
            setTimeout(() => {
                board.children[position].classList.remove('move-animate');
            }, 220);
        }
        // Swap tile with empty position
        [this.tiles[position], this.tiles[this.emptyPos]] = 
        [this.tiles[this.emptyPos], this.tiles[position]];
        this.emptyPos = position;
        this.moves++;
        // Update move counter
        document.getElementById('moveCount').textContent = this.moves;
        // Check if solved
        if (this.isSolved()) {
            this.solved = true;
            this.stopTimer();
            return 'solved';
        }
        return true;
    }
    
    /**
     * Check if puzzle is solved
     */
    isSolved() {
        return this.tiles.every((tile, index) => tile === this.solvedState[index]);
    }
    
    /**
     * Get valid moves (positions that can be moved)
     */
    getValidMoves() {
        const moves = [];
        const row = Math.floor(this.emptyPos / this.size);
        const col = this.emptyPos % this.size;
        
        // Up
        if (row > 0) {
            moves.push(this.emptyPos - this.size);
        }
        // Down
        if (row < this.size - 1) {
            moves.push(this.emptyPos + this.size);
        }
        // Left
        if (col > 0) {
            moves.push(this.emptyPos - 1);
        }
        // Right
        if (col < this.size - 1) {
            moves.push(this.emptyPos + 1);
        }
        
        return moves;
    }
    
    /**
     * Highlight valid moves
     */
    highlightValidMoves() {
        const validMoves = this.getValidMoves();
        document.querySelectorAll('.puzzle-tile').forEach((tile, index) => {
            if (validMoves.includes(index)) {
                tile.classList.add('movable');
            } else {
                tile.classList.remove('movable');
            }
        });
    }
    
    /**
     * Render puzzle on board (optimized)
     */
    render(boardElement) {
        boardElement.className = `puzzle-board size-${this.size}`;
        // If board is empty, create tiles
        if (boardElement.children.length !== this.tiles.length) {
            boardElement.innerHTML = '';
            for (let i = 0; i < this.tiles.length; i++) {
                const tile = document.createElement('div');
                tile.className = 'puzzle-tile';
                tile.dataset.position = i;
                boardElement.appendChild(tile);
            }
        }
        // Update only changed tiles
        for (let i = 0; i < this.tiles.length; i++) {
            const tileValue = this.tiles[i];
            const tile = boardElement.children[i];
            tile.className = 'puzzle-tile';
            tile.dataset.position = i;
            tile.style.backgroundImage = '';
            if (tileValue === 0) {
                tile.classList.add('empty');
                tile.textContent = '';
                tile.removeAttribute('data-value');
            } else {
                tile.classList.add('filled');
                tile.dataset.value = tileValue;
                tile.textContent = tileValue;
                // Try to load image if available (optional)
                const img = new window.Image();
                img.src = `assets/images/tiles/tile-${tileValue}.jpg`;
                img.onload = () => {
                    tile.style.backgroundImage = `url('${img.src}')`;
                    tile.style.backgroundSize = 'cover';
                    tile.style.backgroundPosition = 'center';
                };
            }
        }
        // Highlight valid moves
        this.highlightValidMoves();
    }
    
    /**
     * Calculate optimal moves (Manhattan distance heuristic)
     */
    calculateOptimalMoves() {
        let distance = 0;
        for (let i = 0; i < this.tiles.length; i++) {
            const tileValue = this.tiles[i];
            if (tileValue === 0) continue;
            
            const currentRow = Math.floor(i / this.size);
            const currentCol = i % this.size;
            const goalPos = tileValue - 1;
            const goalRow = Math.floor(goalPos / this.size);
            const goalCol = goalPos % this.size;
            
            distance += Math.abs(currentRow - goalRow) + Math.abs(currentCol - goalCol);
        }
        return distance;
    }
}

// Export for use in game.js
window.Puzzle = Puzzle;

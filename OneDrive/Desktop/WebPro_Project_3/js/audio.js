/**
 * Audio Module
 * Handles background music and sound effects
 */

let musicPlaying = false;
let musicVolume = 0.3;
let sfxVolume = 0.5;

/**
 * Initialize audio
 */
function initAudio() {
    const bgMusic = document.getElementById('bgMusic');
    if (bgMusic) {
        bgMusic.volume = musicVolume;
    }
}

/**
 * Play background music
 */
function playMusic() {
    const bgMusic = document.getElementById('bgMusic');
    if (bgMusic && !musicPlaying) {
        bgMusic.play().catch(err => {
            console.log('Autoplay prevented:', err);
        });
        musicPlaying = true;
    }
}

/**
 * Stop background music
 */
function stopMusic() {
    const bgMusic = document.getElementById('bgMusic');
    if (bgMusic) {
        bgMusic.pause();
        bgMusic.currentTime = 0;
        musicPlaying = false;
    }
}

/**
 * Toggle music
 */
function toggleMusic() {
    if (musicPlaying) {
        stopMusic();
    } else {
        playMusic();
    }
}

/**
 * Play sound effect
 */
function playSound(soundName) {
    const soundMap = {
        'tileClick': 'tileClickSound',
        'move': 'moveSound',
        'victory': 'victorySound',
        'powerup': 'powerupSound',
        'hint': 'hintSound'
    };
    
    const soundId = soundMap[soundName];
    if (!soundId) return;
    
    const sound = document.getElementById(soundId);
    if (sound) {
        sound.volume = sfxVolume;
        sound.currentTime = 0;
        sound.play().catch(err => {
            console.log('Sound play failed:', err);
        });
    }
}

/**
 * Set music volume
 */
function setMusicVolume(volume) {
    musicVolume = Math.max(0, Math.min(1, volume));
    const bgMusic = document.getElementById('bgMusic');
    if (bgMusic) {
        bgMusic.volume = musicVolume;
    }
}

/**
 * Set sound effects volume
 */
function setSFXVolume(volume) {
    sfxVolume = Math.max(0, Math.min(1, volume));
}

/**
 * Intensify music (when time is running out)
 */
function intensifyMusic() {
    const bgMusic = document.getElementById('bgMusic');
    if (bgMusic) {
        bgMusic.playbackRate = 1.2;
        bgMusic.volume = Math.min(1, musicVolume * 1.5);
    }
}

/**
 * Normalize music
 */
function normalizeMusic() {
    const bgMusic = document.getElementById('bgMusic');
    if (bgMusic) {
        bgMusic.playbackRate = 1;
        bgMusic.volume = musicVolume;
    }
}

// Initialize audio on page load
document.addEventListener('DOMContentLoaded', () => {
    initAudio();
    
    // Start music on first user interaction
    document.addEventListener('click', function startMusicOnInteraction() {
        playMusic();
        document.removeEventListener('click', startMusicOnInteraction);
    }, { once: true });
});

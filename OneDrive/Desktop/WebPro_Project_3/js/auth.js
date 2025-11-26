/**
 * Authentication Module
 * Handles login, registration, and session management
 */

// Login form handler
document.getElementById('loginForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const username = document.getElementById('loginUsername').value.trim();
    const password = document.getElementById('loginPassword').value;
    const messageEl = document.getElementById('loginMessage');
    
    if (!username || !password) {
        messageEl.textContent = 'Please fill in all fields';
        messageEl.className = 'form-message error';
        return;
    }
    
    showLoading('Logging in...');
    
    const result = await apiRequest('login', { username, password });
    
    hideLoading();
    
    if (result.success) {
        messageEl.textContent = 'Login successful! Redirecting...';
        messageEl.className = 'form-message success';
        setTimeout(() => {
            window.location.href = 'index.html';
        }, 1000);
    } else {
        messageEl.textContent = result.error || 'Login failed';
        messageEl.className = 'form-message error';
    }
});

// Register form handler
document.getElementById('registerForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const username = document.getElementById('registerUsername').value.trim();
    const email = document.getElementById('registerEmail').value.trim();
    const password = document.getElementById('registerPassword').value;
    const passwordConfirm = document.getElementById('registerPasswordConfirm').value;
    const messageEl = document.getElementById('registerMessage');
    
    // Validation
    if (!username || !email || !password || !passwordConfirm) {
        messageEl.textContent = 'Please fill in all fields';
        messageEl.className = 'form-message error';
        return;
    }
    
    if (password !== passwordConfirm) {
        messageEl.textContent = 'Passwords do not match';
        messageEl.className = 'form-message error';
        return;
    }
    
    if (password.length < 6) {
        messageEl.textContent = 'Password must be at least 6 characters';
        messageEl.className = 'form-message error';
        return;
    }
    
    showLoading('Creating account...');
    
    const result = await apiRequest('register', { username, email, password });
    
    hideLoading();
    
    if (result.success) {
        messageEl.textContent = 'Account created! Logging you in...';
        messageEl.className = 'form-message success';
        
        // Auto-login after registration
        const loginResult = await apiRequest('login', { username, password });
        if (loginResult.success) {
            setTimeout(() => {
                window.location.href = 'index.html';
            }, 1000);
        }
    } else {
        messageEl.textContent = result.error || 'Registration failed';
        messageEl.className = 'form-message error';
    }
});

// Form switching
document.getElementById('showRegister')?.addEventListener('click', (e) => {
    e.preventDefault();
    document.getElementById('loginForm').classList.remove('active');
    document.getElementById('registerForm').classList.add('active');
});

document.getElementById('showLogin')?.addEventListener('click', (e) => {
    e.preventDefault();
    document.getElementById('registerForm').classList.remove('active');
    document.getElementById('loginForm').classList.add('active');
});

// Logout handler
document.querySelectorAll('#logoutBtn').forEach(btn => {
    btn.addEventListener('click', async () => {
        const result = await apiRequest('logout');
        if (result.success) {
            window.location.href = 'login.html';
        }
    });
});

// Check authentication on protected pages
async function checkAuthentication() {
    // Skip check on login page
    if (window.location.pathname.includes('login.html')) {
        return { authenticated: true };
    }
    
    const result = await apiRequest('check_auth');
    
    if (!result.success || !result.authenticated) {
        window.location.href = 'login.html';
        return { authenticated: false };
    } else {
        // Update username display
        document.querySelectorAll('#username').forEach(el => {
            el.textContent = result.user.username;
        });
        
        // Update skill level display
        document.querySelectorAll('#skillLevel').forEach(el => {
            el.textContent = result.user.skill_level;
        });
        
        // Apply user theme
        if (result.user.theme) {
            document.body.setAttribute('data-theme', result.user.theme);
        }
        
        return { authenticated: true, user: result.user };
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.querySelector('form');
    const submitBtn = loginForm.querySelector('button[type="submit"]');
    const errorDiv = document.querySelector('.error');
    const usernameInput = document.getElementById('username');

    if (usernameInput) {
        usernameInput.focus();
    }

    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Reset state
            if (errorDiv) {
                errorDiv.style.display = 'none';
                errorDiv.textContent = '';
            }
            
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner"></span> Memproses...';
            submitBtn.style.opacity = '0.8';
            submitBtn.style.cursor = 'not-allowed';

            const formData = new FormData(loginForm);
            
            try {
                const response = await fetch(loginForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok && data.redirect) {
                    // Success! Immediate redirect
                    submitBtn.innerHTML = 'Login Berhasil! Mengalihkan...';
                    submitBtn.style.background = '#81C784'; // Success color
                    window.location.href = data.redirect;
                } else {
                    // Handle errors
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.style.opacity = '1';
                    submitBtn.style.cursor = 'pointer';
                    
                    if (errorDiv) {
                        errorDiv.textContent = data.error || data.message || 'Terjadi kesalahan. Silakan coba lagi.';
                        errorDiv.style.display = 'block';
                    } else {
                        // If error div doesn't exist, create it
                        const newError = document.createElement('div');
                        newError.className = 'error';
                        newError.textContent = data.error || data.message || 'Terjadi kesalahan.';
                        loginForm.before(newError);
                    }
                }
            } catch (error) {
                console.error('Login error:', error);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
                submitBtn.style.opacity = '1';
                submitBtn.style.cursor = 'pointer';
                
                alert('Gagal menghubungi server. Periksa koneksi internet Anda.');
            }
        });
    }
});

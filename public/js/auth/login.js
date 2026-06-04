document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const submitBtn = loginForm?.querySelector('button[type="submit"]');
    const errorDiv = document.querySelector('.error');
    const usernameInput = document.getElementById('username');
    const csrfInput = loginForm?.querySelector('input[name="_token"]');
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');

    const setError = (message) => {
        if (!errorDiv) return;
        errorDiv.style.display = 'block';
        errorDiv.textContent = message;
    };

    const clearError = () => {
        if (!errorDiv) return;
        errorDiv.style.display = 'none';
        errorDiv.textContent = '';
    };

    const setBusy = (busy) => {
        if (!submitBtn) return;
        submitBtn.disabled = busy;
        submitBtn.innerHTML = busy ? '<span class="spinner"></span> Memproses...' : 'Masuk Dashboard';
        submitBtn.style.opacity = busy ? '0.8' : '';
        submitBtn.style.cursor = busy ? 'not-allowed' : '';
    };

    const syncCsrfToken = async () => {
        try {
            const res = await fetch('/csrf-token', {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                credentials: 'same-origin',
                cache: 'no-store',
            });

            if (!res.ok) {
                return null;
            }

            const data = await res.json();
            if (data?.token) {
                if (csrfInput) csrfInput.value = data.token;
                if (csrfMeta) csrfMeta.setAttribute('content', data.token);
                return data.token;
            }
        } catch (error) {
            console.error('Failed to refresh CSRF token', error);
        }

        return null;
    };

    const submitLogin = async (event) => {
        event.preventDefault();
        clearError();
        setBusy(true);

        const sendRequest = async (retry = false) => {
            if (!csrfInput?.value || retry) {
                await syncCsrfToken();
            }

            const formData = new FormData(loginForm);
            const response = await fetch(loginForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            });

            if (response.status === 419 && !retry) {
                await syncCsrfToken();
                return sendRequest(true);
            }

            let payload = {};
            try {
                payload = await response.json();
            } catch (error) {
                payload = {};
            }

            if (response.ok && payload.redirect) {
                window.location.href = payload.redirect;
                return;
            }

            if (response.status === 422 && payload.errors?.username?.length) {
                setError(payload.errors.username[0]);
                return;
            }

            if (response.status === 403 && payload.error) {
                setError(payload.error);
                return;
            }

            if (payload.error) {
                setError(payload.error);
                return;
            }

            setError('Login gagal. Coba muat ulang halaman lalu login lagi.');
        };

        try {
            await sendRequest(false);
        } catch (error) {
            console.error('Login submit failed', error);
            setError('Terjadi gangguan saat login. Coba muat ulang halaman.');
        } finally {
            setBusy(false);
        }
    };

    if (usernameInput) {
        usernameInput.focus();
    }

    if (loginForm) {
        window.addEventListener('pageshow', async (event) => {
            if (event.persisted) {
                window.location.reload();
                return;
            }

            await syncCsrfToken();
        });

        loginForm.addEventListener('submit', submitLogin);
    }
});

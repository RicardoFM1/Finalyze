import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';


export const useAuthStore = defineStore('auth', () => {
    const storedUser = localStorage.getItem('user');
    const user = ref(null);
    try {
        if (storedUser && storedUser !== 'undefined') {
            user.value = JSON.parse(storedUser);
        }
    } catch (e) {
        localStorage.removeItem('user');
    }
    const token = ref(localStorage.getItem('token') || null);
    const API_URL = 'http://localhost:8000/api';
    const router = useRouter();

    const isAuthenticated = computed(() => !!token.value);

    async function apiFetch(endpoint, options = {}) {
        const url = endpoint.startsWith('http') ? endpoint : `${API_URL}${endpoint}`;

        const headers = {
            'Accept': 'application/json',
            ...options.headers
        };

        if (token.value) {
            headers['Authorization'] = `Bearer ${token.value}`;
        }

        if (options.body && !(options.body instanceof FormData) && !headers['Content-Type']) {
            headers['Content-Type'] = 'application/json';
        }

        try {
            const response = await fetch(url, { ...options, headers });

            if (response.status === 401) {
                logout();
                router.push('/login');
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || 'Sessão expirada. Faça login novamente.');
            }

            if (response.status === 403) {
                router.push('/planos');
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || 'Acesso negado. Verifique seu plano.');
            }

            return response;
        } catch (error) {
            console.error('Erro na requisição API:', error);
            throw error;
        }
    }

    async function login(email, password) {
        try {
            const response = await apiFetch('/auth/login', {
                method: 'POST',
                body: JSON.stringify({ email, password })
            });

            const data = await response.json();

            if (!response.ok) throw new Error(data.message || 'Falha no login');

            token.value = data.access_token;
            user.value = data.user;
            localStorage.setItem('token', token.value);
            localStorage.setItem('user', JSON.stringify(user.value));

            return true;
        } catch (error) {
            console.error(error);
            throw error;
        }
    }

    async function register(name, email, password, password_confirmation) {
        try {
            const response = await apiFetch('/auth/register', {
                method: 'POST',
                body: JSON.stringify({ name, email, password, password_confirmation })
            });

            const data = await response.json();

            if (!response.ok) throw new Error(data.message || 'Falha no cadastro (verifique senhas ou email)');

            token.value = data.access_token;
            localStorage.setItem('token', token.value);

            await fetchUser();

            return true;
        } catch (error) {
            console.error(error);
            throw error;
        }
    }

    async function fetchUser() {
        if (!token.value) return;
        try {
            const response = await apiFetch('/user');
            if (response.ok) {
                const data = await response.json();
                user.value = data;
                localStorage.setItem('user', JSON.stringify(user.value));
            }
        } catch (e) {
            console.error(e);
        }
    }

    function logout() {
        token.value = null;
        user.value = null;
        localStorage.removeItem('token');
        localStorage.removeItem('user');
    }

    return { user, token, isAuthenticated, login, register, logout, fetchUser, apiFetch };
});

import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useUiStore } from './ui';


export const useAuthStore = defineStore('auth', () => {

    const user = ref(null);

    const token = ref(localStorage.getItem('token') || null);
    const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';
    const router = useRouter();

    const isAuthenticated = computed(() => !!token.value);

    const hasActivePlan = computed(() => {
        if (user.value?.admin) return true;
        return !!(user.value?.plano && user.value.plano.recursos && user.value.plano.recursos.length > 0);
    });

    async function apiFetch(endpoint, options = {}) {
        const ui = useUiStore();
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

                if (router.currentRoute.value.name !== 'Login' &&
                    router.currentRoute.value.name !== 'Register' &&
                    router.currentRoute.value.name !== 'Checkout') {
                    router.push({ name: 'Login' });
                }
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || 'Sessão expirada. Faça login novamente.');
            }

            if (response.status === 403) {
                if (router.currentRoute.value.name !== 'Plans') {
                    router.push({ name: 'Plans' });
                }
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || 'Acesso negado. Verifique seu plano.');
            }

            return response;
        } catch (error) {
            console.error('Erro na requisição API:', error);
            throw error;
        }
    }

    async function login(email, senha) {
        try {
            const response = await apiFetch('/auth/login', {
                method: 'POST',
                body: JSON.stringify({ email, senha })
            });

            const data = await response.json();

            if (!response.ok) throw new Error(data.message || 'Falha no login');

            if (data.requer_verificacao) {
                return { requer_verificacao: true, email: data.email };
            }

            token.value = data.access_token;
            user.value = data.usuario;
            localStorage.setItem('token', token.value);
            return { success: true };
        } catch (error) {
            console.error(error);
            throw error;
        }
    }

    async function register(nome, email, senha, password_confirmation, cpf, data_nascimento) {
        try {
            const response = await apiFetch('/auth/register', {
                method: 'POST',
                body: JSON.stringify({ nome, email, senha, senha_confirmation: password_confirmation, cpf, data_nascimento })
            });

            const data = await response.json();

            if (!response.ok) throw new Error(data.message || 'Falha no cadastro (verifique senhas ou email)');

            await fetchUser();

            return true;
        } catch (error) {
            console.error(error);
            throw error;
        }
    }

    async function fetchUser() {
        if (!token.value) return;
        const ui = useUiStore();
        ui.setLoading(true);
        try {
            const response = await apiFetch('/usuario');
            if (response.ok) {
                const data = await response.json();
                user.value = data;
            }
        } catch (e) {
            console.error(e);
        } finally {
            ui.setLoading(false);
        }
    }

    function logout() {
        token.value = null;
        user.value = null;
        localStorage.removeItem('token');
    }

    function hasFeature(featureSlug) {
        if (user.value?.admin) return true;

        if (!user.value?.plano?.recursos) return false;

        const features = user.value.plano.recursos;

        const normalize = str => str?.toString().toLowerCase().normalize('NFD').replace(/[^\w]/g, '');
        const target = normalize(featureSlug);
        return features.some(f => {
            return normalize(f.slug) === target || normalize(f.nome) === target;
        });
    }

    async function verifyCode(email, codigo) {
        try {
            const response = await apiFetch('/auth/verificar', {
                method: 'POST',
                body: JSON.stringify({ email, codigo })
            });

            const data = await response.json();

            if (!response.ok) throw new Error(data.message || 'Falha na verificação');

            token.value = data.access_token;
            user.value = data.usuario;
            localStorage.setItem('token', token.value);
            return true;
        } catch (error) {
            console.error(error);
            throw error;
        }
    }

    async function resendCode(email) {
        try {
            const response = await apiFetch('/auth/reenviar', {
                method: 'POST',
                body: JSON.stringify({ email })
            });

            const data = await response.json();

            if (!response.ok) throw new Error(data.message || 'Falha ao reenviar código');

            return true;
        } catch (error) {
            console.error(error);
            throw error;
        }
    }

    const getStorageUrl = (path) => {
        if (!path) return null;
        if (path.startsWith('http')) return path;
        const baseUrl = API_URL.replace('/api', '');
        return `${baseUrl}/storage/${path}`;
    }

    return { user, token, isAuthenticated, hasActivePlan, login, register, verifyCode, resendCode, logout, fetchUser, apiFetch, hasFeature, getStorageUrl };
});

import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useUiStore } from './ui';
import { toast } from 'vue3-toastify';


export const useAuthStore = defineStore('auth', () => {

    const user = ref(null);
    const workspaceId = ref(localStorage.getItem('workspace_id') || null);
    const sharedAccounts = ref([]);
    const loadingSharedAccounts = ref(false);
    const mustVerifyEmail = ref(null);
    const mustCompleteRegistration = ref(null);

    const token = ref(localStorage.getItem('token') || null);
    const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';
    const router = useRouter();

    const isAuthenticated = computed(() => !!token.value);
    const activeWorkspace = computed(() => {
        return sharedAccounts.value.find(acc => acc.id == workspaceId.value);
    });

    const hasActivePlan = computed(() => {
        if (user.value?.admin) return true;
        return !!(user.value?.plano && user.value.plano.recursos && user.value.plano.recursos.length > 0);
    });

    async function apiFetch(endpoint, options = {}) {
        const ui = useUiStore();
        const url = endpoint.startsWith('http') ? endpoint : `${API_URL}${endpoint}`;
        const {
            suppress403Redirect = false,
            suppress403Toast = false,
            ...fetchOptions
        } = options;

        const headers = {
            'Accept': 'application/json',
            ...fetchOptions.headers
        };

        if (token.value) {
            headers['Authorization'] = 'Bearer ' + token.value;
        }

        if (workspaceId.value) {
            headers['X-Workspace-Id'] = workspaceId.value;
        }

        if (fetchOptions.body && !(fetchOptions.body instanceof FormData) && !headers['Content-Type']) {
            headers['Content-Type'] = 'application/json';
        }

        try {
            const response = await fetch(url, { ...fetchOptions, headers });

            if (response.status === 401) {
                logout();

                if (router.currentRoute.value.name !== 'Login' &&
                    router.currentRoute.value.name !== 'Register' &&
                    router.currentRoute.value.name !== 'Checkout') {
                    router.push({ name: 'Login' });
                }
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || 'Sess�o expirada. Fa�a login novamente.');
            }

            if (response.status === 403) {
                const errorData = await response.json().catch(() => ({}));
                const message = errorData.message || 'Seu plano atual n�o possui acesso a este recurso.';

                if (!suppress403Toast && router.currentRoute.value.name !== 'Plans') {
                    toast.warning(message, { autoClose: 5000 });
                }
                if (!suppress403Redirect && router.currentRoute.value.name !== 'Plans') {
                    router.push({ name: 'Plans' });
                }
                throw new Error(message);
            }

            return response;
        } catch (error) {
            console.error('Erro na requisi��o API:', error);
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
                const clearAuthModals = () => {
        mustVerifyEmail.value = null;
        mustCompleteRegistration.value = null;
    };

    return { requer_verificacao: true, email: data.email };
            }

            token.value = data.access_token;
            user.value = data.usuario;
            localStorage.setItem('token', token.value);
            await fetchSharedAccounts();
            const clearAuthModals = () => {
        mustVerifyEmail.value = null;
        mustCompleteRegistration.value = null;
    };

    return { success: true };
        } catch (error) {
            console.error(error);
            throw error;
        }
    }

    async function register(nome, email, senha, password_confirmation, cpf, data_nascimento, aceita_termos = false, aceita_notificacoes = true) {
        try {
            const response = await apiFetch('/auth/register', {
                method: 'POST',
                body: JSON.stringify({
                    nome,
                    email,
                    senha,
                    senha_confirmation: password_confirmation,
                    cpf,
                    data_nascimento,
                    aceita_termos: Boolean(aceita_termos),
                    aceita_notificacoes: Boolean(aceita_notificacoes)
                })
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

    async function fetchUser(force = false) {
        if (!token.value || (user.value && !force)) return;
        const ui = useUiStore();
        ui.setLoading(true);
        try {
            const response = await apiFetch('/usuario');
            if (response.ok) {
                const data = await response.json();
                user.value = data;

                // Initialize sharedAccounts with self immediately
                sharedAccounts.value = [{ id: data.id, owner: data, is_owner: true }];

                if (!workspaceId.value || workspaceId.value == 'null') {
                    workspaceId.value = data.id;
                    localStorage.setItem('workspace_id', data.id);
                }
                // Fetch collaborations and wait for it
                await fetchSharedAccounts();
            }
        } catch (e) {
            console.error(e);
        } finally {
            ui.setLoading(false);
        }
    }

    async function fetchSharedAccounts() {
        if (!token.value || loadingSharedAccounts.value) return;
        loadingSharedAccounts.value = true;
        try {
            const response = await apiFetch('/colaboracoes');
            if (response.ok) {
                const data = await response.json();

                // If user.value is not available yet, we skip populating but don't fail
                if (!user.value) {
                    sharedAccounts.value = [];
                    return;
                }

                const ownAccount = { id: user.value.id, owner: user.value, is_owner: true };
                const otherAccounts = (data.shared_with_me || []).map(s => ({
                    ...s,
                    id: s.proprietario_id,
                    owner: s.proprietario,
                    is_owner: false
                }));

                sharedAccounts.value = [ownAccount, ...otherAccounts];

                // Check if current workspace is still valid among all accounts
                const stillExists = sharedAccounts.value.some(a => a.id == workspaceId.value);
                if (!stillExists || !workspaceId.value || workspaceId.value == 'null') {
                    workspaceId.value = user.value.id;
                    localStorage.setItem('workspace_id', user.value.id);
                }
            }
        } catch (e) {
            console.error('Erro ao buscar contas compartilhadas:', e);
        } finally {
            loadingSharedAccounts.value = false;
        }
    }

    function setWorkspace(id) {
        workspaceId.value = id;
        localStorage.setItem('workspace_id', id);
        window.location.reload(); // Reload to refresh all data context
    }

    function logout() {
        token.value = null;
        user.value = null;
        workspaceId.value = null;
        localStorage.removeItem('token');
        localStorage.removeItem('workspace_id');
    }

    function hasFeature(featureSlug) {
        const normalize = str => str?.toString().toLowerCase().normalize('NFD').replace(/[^\w]/g, '');
        const target = normalize(featureSlug);

        // Bloqueio explícito de Admin para colaboradores
        const isShared = activeWorkspace.value && !activeWorkspace.value.is_owner;
        if (target === 'admin' && isShared) return false;

        const targetUser = activeWorkspace.value?.owner || user.value;

        if (targetUser?.admin) return true;

        if (!targetUser?.plano?.recursos) return false;

        const features = targetUser.plano.recursos;

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
        if (path.startsWith('data:')) return path; // Base64
        const baseUrl = API_URL.replace('/api', '');
        return `${baseUrl}/storage/${path}`;
    }

    const ui = useUiStore();

    function setLanguage(lang) {
        ui.setLocale(lang);
        localStorage.setItem('locale', lang);
        if (typeof window !== 'undefined') {
            // Optional: for fully reactive and persistent components that don't watch the store
            const msg = lang === 'pt' ? 'Idioma alterado para Português' : 'Language changed to English';
            toast.success(msg, { autoClose: 1000 });
        }
    }

    async function googleLogin() {
        window.location.href = `${API_URL}/auth/google`;
    }

    async function handleGoogleCallback() {
        try {
            const response = await apiFetch('/auth/google/callback');
            if (response.ok) {
                return await response.json();
            }
            throw new Error('Erro ao processar login com Google');
        } catch (e) {
            console.error(e);
            throw e;
        }
    }

    async function completarCadastroSocial(dados) {
        try {
            const response = await apiFetch('/auth/google/completar', {
                method: 'POST',
                body: JSON.stringify(dados)
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Falha ao completar cadastro');
            return data;
        } catch (error) {
            console.error(error);
            throw error;
        }
    }

    async function forgotPassword(email) {
        try {
            const response = await apiFetch('/auth/forgot-password', {
                method: 'POST',
                body: JSON.stringify({ email })
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Falha ao solicitar recuperação');
            return true;
        } catch (error) {
            console.error(error);
            throw error;
        }
    }

    async function resetPassword(dados) {
        try {
            const response = await apiFetch('/auth/reset-password', {
                method: 'POST',
                body: JSON.stringify(dados)
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Falha ao redefinir senha');
            return true;
        } catch (error) {
            console.error(error);
            throw error;
        }
    }

    const clearAuthModals = () => {
        mustVerifyEmail.value = null;
        mustCompleteRegistration.value = null;
    };

    return {
        user, token, workspaceId, sharedAccounts, activeWorkspace, isAuthenticated, hasActivePlan,
        login, register, verifyCode, resendCode, logout, fetchUser, apiFetch, hasFeature,
        getStorageUrl, setWorkspace, fetchSharedAccounts, setLanguage,
        googleLogin, handleGoogleCallback, completarCadastroSocial, forgotPassword, resetPassword, mustVerifyEmail, mustCompleteRegistration, clearAuthModals
    };
});




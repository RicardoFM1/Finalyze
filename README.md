# Finalyze - Sistema de Gerenciamento Financeiro

Finalyze é uma plataforma robusta de controle financeiro pessoal, permitindo que usuários monitorem suas receitas, despesas e planejem seu futuro financeiro através de diversos planos de assinatura.

## Funcionalidades Principais

### Backend (Laravel 11)
O backend é uma API RESTful construída com Laravel, focada em segurança, processamento de pagamentos e gerenciamento de usuários.

- **Autenticação**: Sistema completo de login e registro usando Laravel Sanctum para tokens de API.
- **Gerenciamento de Planos**: API para listar, criar, atualizar e excluir planos de assinatura (acesso restrito a administradores).
- **Integração Mercado Pago**: 
    - Geração de preferências de pagamento para Checkout Pro.
    - Processamento direto via API (Payment Brick) para PIX e Cartão de Crédito.
    - **Expiração de PIX**: QR Codes expiram automaticamente em 10 minutos para garantir a consistência das sessões.
    - **Webhooks**: Recebimento de notificações em tempo real para ativação automática de planos após confirmação de pagamento.
- **Middlewares de Segurança**:
    - `EnsureUserHasPlan`: Bloqueia o acesso a funcionalidades do dashboard se o usuário não possuir um plano ativo.
    - `EnsureUserIsAdmin`: Restringe rotas administrativas para usuários com o papel de 'admin'.
- **Manutenção de Assinaturas**:
    - Lógica de ativação/atualização de plano do usuário no banco de dados.
    - Endpoint para cancelamento de assinaturas pendentes, permitindo que o usuário mude de plano antes de concluir um pagamento aberto.

### Frontend (Vue 3 + Vuetify)
Uma interface moderna, responsiva e dinâmica construída com Vue 3, Pinia para gerenciamento de estado e Vuetify como framework de UI.

#### Páginas e Lógicas:
- **Home**: Página de entrada com visão geral dos benefícios e acesso rápido ao login ou seleção de planos. Possui botões inteligentes que detectam se o usuário já está logado ou se já possui um plano.
- **Planos (Plans.vue)**:
    - Exibição dinâmica de cards de planos.
    - **Lógica de Detecção Pendente**: Ao entrar, o sistema verifica se o usuário tem um pagamento em aberto. Se sim, exibe um diálogo permitindo "Continuar" ou "Cancelar Anterior".
    - **PlanCard Component**: Botões dinâmicos que mostram "Plano Atual" (se já ativo) ou "Mudar para [Plano]" (se for um upgrade/change).
- **Checkout (PaymentBrick.vue)**:
    - Integração de segurança com Mercado Pago Brick.
    - Redirecionamentos automáticos e Toasts de sucesso após a confirmação.
    - Sistema de **Polling** para PIX: Verifica o status do pagamento em tempo real para liberar o acesso assim que pago.
- **Dashboard (Painel)**: 
    - Acesso centralizado às métricas financeiras.
    - Protegido por guardas de rota que verificam a autenticação e o plano ativo.
- **Perfil**: Área para o usuário gerenciar seus dados pessoais e ver o status do seu plano atual.

## Tecnologias Utilizadas

- **Backend**: Laravel 11, PHP 8.2+, MySQL, Mercado Pago SDK.
- **Frontend**: Vue.js 3 (Composition API), Vite, Vuetify 3, Pinia, Vue Router, Vue3-Toastify.
- **DevOps/Ferramentas**: Axios para requisições, NGROK (para testes de Webhook locais).

---

## Como Rodar o Projeto

1. **Backend**:
   ```bash
   composer install
   php artisan migrate --seed
   php artisan serve
   ```
2. **Frontend**:
   ```bash
   npm install
   npm run dev
   ```
3. **Webhooks**:
   Utilize o NGROK para expor a URL local e configure o `notification_url` no `CheckoutController.php`.

---

## Documentação Técnica do Projeto (Implementações Recentes)

### 1. Sistema de Segurança e 2FA
- **Fluxo**: Ao registrar, um código de 6 dígitos é enviado ao e-mail. O acesso total ao sistema só é liberado após a verificação desse código.
- **Expiração**: O código tem validade de 15 minutos.
- **Status**: O usuário é redirecionado para `EmailVerification.vue` até que a conta esteja ativa.

### 2. Gestão de Planos e Lançamentos
- **Validação de Limites**: O sistema impede a criação de novos lançamentos se o usuário atingir o limite do seu plano (`limite_lancamentos`).
- **Reatividade no Checkout**: O `PaymentBrick` é destruído e recriado automaticamente se o usuário mudar de plano na URL durante o checkout, garantindo valores sempre corretos.

### 3. Inativação de Itens (Soft Delete)
- Metas e Anotações agora são "Desativadas" em vez de excluídas.
- Itens inativos podem ser consultados na aba dedicada e reativados a qualquer momento.

---

## Comandos Administrativos (CLI)

### Designar Assidantura Manualmente
Útil para assinaturas via Pix externo, cortesia ou testes.
```bash
php artisan app:designar-assinatura {usuario_id} {plano_id}
```
**Atenção**: Não utilize chaves `{}` ao executar. Exemplo: `php artisan app:designar-assinatura 1 2`

> [!NOTE]
> Se você receber o erro `No query results for model [App\Models\Usuario]`, significa que o ID do usuário informado não existe no banco de dados.
> Para listar seus usuários e ver os IDs corretos, você pode rodar:
> `php artisan tinker --execute="print_r(App\Models\Usuario::all(['id', 'email'])->toArray())"`

---

## Validações de E-mail
- **Frontend**: Regex rigoroso impede e-mails mal formatados ou compostos apenas por números.
- **Backend**: Validação `email:strict` configurada no `RegisterRequest.php`.


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
- **Gerenciamento Automático de Assinaturas**:
    - **Expiração Automática**: Command agendado (`app:verificar-assinaturas-expiradas`) que roda diariamente à meia-noite para expirar assinaturas vencidas e remover planos de usuários.
    - **Lembretes de Renovação por Email**: 
        - Sistema de notificações automáticas enviado 2x ao dia (9h e 18h).
        - Email amigável 3 dias antes da expiração com detalhes da assinatura.
        - Email urgente 1 dia antes com contador de horas restantes.
        - Templates profissionais e responsivos com links diretos para renovação.
        - Sistema anti-duplicação para evitar múltiplos envios.
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

4. **Scheduler (Produção)**:
   Para ativar os comandos de expiração e lembretes de renovação, adicione ao cron:
   ```bash
   * * * * * cd /caminho/para/finalyze/backend && php artisan schedule:run >> /dev/null 2>&1
   ```
   Ou em desenvolvimento, rode continuamente:
   ```bash
   php artisan schedule:work
   ```

5. **Configuração de Email**:
   Configure as variáveis de email no `.env` para ativar os lembretes de renovação:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=seu_servidor_smtp
   MAIL_PORT=587
   MAIL_USERNAME=seu_usuario
   MAIL_PASSWORD=sua_senha
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=noreply@finalyze.com
   APP_FRONTEND_URL=http://localhost:5173
   ```

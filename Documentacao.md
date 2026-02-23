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

### 4. Gestão Avançada de Assinaturas (Prorrata e Upgrades)
- **Cálculo de Prorrata**: Ao mudar de plano, o tempo restante do plano atual é convertido em crédito monetário.
- **Upgrades Gratuitos**: Se o crédito acumulado cobrir o valor total do novo plano, a ativação ocorre instantaneamente via modal na página de Planos, sem necessidade de ir ao checkout.
- **Acúmulo de Dias**: Renovação do mesmo plano soma os dias à data de expiração atual; mudança de plano (upgrade) reseta o ciclo usando o desconto calculado.

### 5. Multi-Workspace e Colaboração (Collaboration System)
- **Conceito de Workspace**: Cada usuário possui um "Workspace" (o ID do usuário é o ID do Workspace). Todos os dados (lançamentos, metas, lembretes) pertencem a esse ID.
- **Fluxo de Convite**:
    1. O **Proprietário** faz um convite via e-mail na aba de Colaboradores.
    2. O sistema cria um registro na tabela `colaboracoes` com o `proprietario_id` e o `email_convidado`.
    3. O Convidado recebe um e-mail de convite traduzido.
- **Chaveamento de Contexto (Context Switching)**:
    - O Convidado, ao selecionar um workspace compartilhado, faz o frontend enviar o header `X-Workspace-Id: [ID_DO_PROPRIETARIO]` em todas as requisições.
- **Middleware de Contexto (`SetWorkspaceContext.php`)**:
    - Valida se o usuário autenticado tem permissão para acessar o workspace solicitado (via tabela `colaboracoes`).
    - Injeta o ID do dono em um Singleton: `app()->instance('workspace_id', $id)`.
- **Lógica de Permissões Herdadass**:
    - O colaborador herda **todas as funcionalidades do plano do proprietário** enquanto estiver em seu workspace.
    - Se o dono for Premium, o colaborador tem acesso ao Finn AI e Relatórios, mesmo que o plano pessoal do colaborador seja Gratuito.
    - **Restrição de Admin**: A página de Administração (`/admin`) é bloqueada explicitamente para colaboradores, mesmo que o proprietário seja um administrador. Apenas o dono real da conta acessa as funções administrativas.
- **Arquitetura de Serviços e Middlewares**:
    - **Middlewares (`CheckResource`, `EnsureUserHasPlan`)**: Atualizados para verificar o plano do **Dono do Workspace** em vez do usuário logado.
    - **Serviços**: Utilizam `app('workspace_id')` para garantir que qualquer ação (Criar Lançamento, Editar Meta, etc.) seja persistida na conta do proprietário correta.
- **Endpoints**: Substituídos `/shared-accounts` por `/colaboracoes` para manter a consistência com o idioma do projeto.

> [!NOTE]
> Para detalhes técnicos de implementação da prorrata e endpoints financeiros, consulte o arquivo [DOCUMENTACAO_ASSINATURA.md](file:///c:/Users/Pessoal/Desktop/Programação/Finalyze/backend/DOCUMENTACAO_ASSINATURA.md).

### 6. Sistema Multi-Idioma no Backend
- **Idioma Nativo**: Adicionada a coluna `idioma` na tabela `usuarios` para persistir a preferência (EN/PT) do usuário.
- **Middleware `SetLocale.php`**: Detecta o idioma preferido através do header `Accept-Language` ou da configuração no perfil do usuário, ajustando as respostas do servidor e templates de e-mail dinamicamente.
- **Templates de Email Bilíngues**: Todos os e-mails (Verificação, Convites, Lembretes) utilizam strings traduzíveis do Laravel (`__()`), garantindo uma experiência premium e globalizada.

### 7. Finn AI - Assistente Financeiro com Memória
- **Inteligência Artificial**: Utiliza o modelo **Gemini 1.5 Flash** do Google para respostas ultrarrápidas e precisas.
- **Persistência**: Histórico de chat completo salvo no banco de dados (`mensagens_chat`).
- **Interatividade**: Edição e exclusão de mensagens do usuário integradas.
- **Contexto Financeiro Inteligente**: Em cada interação, o Finn recebe automaticamente:
    - **Saldo e Resumo**: Receitas e despesas do mês atual.
    - **Metas**: Títulos e valores das metas ativas do usuário.
    - **Assinatura**: Detalhes do plano atual e data de renovação.
    - **Pagamentos**: Resumo dos últimos 3 pagamentos realizados.
    - **Upgrades**: Opções de planos disponíveis para facilitar a decisão do usuário.
    - **Histórico**: As últimas 15 mensagens formatadas para manter o contexto da conversa.
- **Cloud Storage**: Integração com **Supabase Storage** para persistência de avatares.

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
        - **Expiração Automática**: Command agendado (`app:verificar-assinaturas-expiradas`) que agora roda a **cada 30 minutos** para garantir que o acesso seja removido quase instantaneamente após o vencimento.
        - **Lembretes de Renovação por Email**: 
            - Verificação automática executada a **cada 30 minutos** (anteriormente 2x ao dia).
            - Email amigável 3 dias antes da expiração com detalhes da assinatura.
            - Email urgente 1 dia antes com contador de horas restantes.
            - Templates profissionais e responsivos (Blade) com links diretos para renovação.
            - Sistema anti-duplicação para evitar múltiplos envios dentro do mesmo ciclo.
    - **Lembretes Pessoais**: Command `app:enviar-lembretes-pessoais` executado **minuto a minuto** para disparar notificações de agenda e compromissos.
    - **Manutenção de Assinaturas**:
        - Lógica de ativação/atualização de plano do usuário no banco de dados.
        - **Limpeza de Pendências**: Endpoint para cancelamento de assinaturas pendentes que realiza um "wipeout" em qualquer `HistoricoPagamento` órfão para garantir que o usuário não tenha cobranças duplicadas ou estados inconsistentes.

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
    - **Conversão de Moeda**: Suporte integrado ao `useMoney`, permitindo checkout em USD, EUR ou BRL com conversão baseada em taxas de câmbio em tempo real.
    - Redirecionamentos automáticos e Toasts de sucesso após a confirmação.
    - Sistema de **Polling** para PIX: Verifica o status do pagamento em tempo real para liberar o acesso assim que pago.
- **Dashboard (Painel)**: 
    - **Visão Geral**: Por padrão, o painel agora exibe o acumulado histórico (Visão Geral) de receitas e despesas para garantir consistência total com o saldo bancário. O filtro mensal pode ser aplicado manualmente.
    - Protegido por guardas de rota que verificam a autenticação e o plano ativo.
- **Moedas**: Centralização da lógica de formatação no composable `useMoney.js`, tratando BRL como moeda base no banco de dados e convertendo dinamicamente para exibição.
- **UX - Skeleton Loading**: Implementado sistema de carregamento estruturado na **Home** e **Perfil**, eliminando transições bruscas e melhorando a percepção de performance.
- **Perfil**: Área para o usuário gerenciar seus dados pessoais e ver o status do seu plano atual com indicadores de progresso de vigência.
- **Checkout State Management**: Controle global de carregamento (`pageLoading`) que garante que o usuário só veja o formulário de pagamento após todos os dados de prorrata e preferências estarem totalmente sincronizados.

## Tecnologias Utilizadas

- **Backend**: Laravel 11, PHP 8.2+, PostgreSQL, Mercado Pago SDK.
- **Frontend**: Vue.js 3 (Composition API), Vite, Vuetify 3, Pinia, Vue Router, Vue3-Toastify.
- **DevOps/Ferramentas**: Axios para requisições, NGROK (para testes de Webhook locais).
- **RocketLog**: Análise e controle de logs.
- **Supabase**: Banco de dados e armazenamento de avatares.
- **Google Cloud**: Gerenciador de emails.

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

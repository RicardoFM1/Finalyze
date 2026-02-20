# Utilitários do Sistema (Artisan Commands)

Este documento descreve os comandos personalizados criados para a administração e manutenção do sistema.

## Comandos Disponíveis

### 1. Designar Assinatura Manualmente
Utilizado para atribuir um plano a um usuário sem passar pelo fluxo de pagamento (ex: cortesia ou correção manual).
- **Comando**: `php artisan app:designar-assinatura {user_id} {plano_id} {periodo_id?}`
- **Argumentos**:
    - `user_id`: ID do usuário no banco de dados.
    - `plano_id`: ID do plano desejado.
    - `periodo_id` (opcional): ID do período (mensal, anual, etc). Se omitido, usa o primeiro disponível.
- **O que faz**: Cancela assinaturas ativas anteriores e cria uma nova assinatura com status `active` válida por 30 dias.

### 2. Verificar Assinaturas Expiradas
Rotina de limpeza para desativar acessos de quem já passou da data de término.
- **Comando**: `php artisan app:verificar-assinaturas-expiradas`
- **O que faz**: Busca assinaturas `active` cuja `termina_em` é menor que `agora`, muda o status para `expired` e remove o `plano_id` do usuário.

### 3. Enviar Avisos de Renovação
Envia e-mails automáticos para o usuário não esquecer da renovação.
- **Comando**: `php artisan app:enviar-avisos-renovacao`
- **O que faz**:
    - Envia um aviso amigável faltando 3 dias.
    - Envia um aviso urgente faltando 1 dia.
    - Registra o envio na tabela `avisos_enviados` para não repetir.

### 4. Autenticação Gmail
Configura o token de acesso para o envio de e-mails via API do Gmail.
- **Comando**: `php artisan gmail:auth`
- **O que faz**: Gera a URL de autorização do Google e solicita o código de retorno para gerar o `GMAIL_REFRESH_TOKEN`.

---

## Segurança

### Estes comandos são seguros?
**Sim, desde que utilizados corretamente.**

1. **Acesso Físico/SSH**: Estes comandos só podem ser executados por quem tem acesso ao terminal do servidor. Eles não estão "abertos" na internet.
2. **Sem Exposição Web**: O código do sistema **não** chama estes comandos via rotas HTTP (Web). Eles são exclusivamente para uso administrativo via linha de comando (CMD/Terminal).
3. **Riscos de Terceiros**: Se você permitir que outra pessoa execute comandos no seu servidor (via acesso SSH), ela poderá usar estes comandos. A segurança depende do controle de acesso ao seu servidor.
4. **Prevenção**: Nunca use `Artisan::call()` em um `Controller` passando dados diretamente do usuário sem validação rigorosa. No estado atual, o sistema está seguindo as melhores práticas.

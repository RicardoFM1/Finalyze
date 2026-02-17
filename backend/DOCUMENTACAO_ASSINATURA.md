# Documentação da Nova Lógica de Assinatura

Este documento detalha como funciona a nova lógica de **Prorrata** e **Upgrades** implementada no Finalyze.

## 1. Cálculo de Prorrata (Créditos)
Quando um usuário decide mudar de plano (upgrade ou downgrade) antes do vencimento atual, o sistema não "perde" o tempo restante.

- **Classe**: `App\Servicos\Checkout\CalculadoraProrata`
- **Lógica**:
    1. O sistema verifica quantos dias faltam para a assinatura atual expirar.
    2. Calcula o valor financeiro desses dias com base no último pagamento realizado.
    3. Esse valor é retornado como **Crédito de Prorrata**.

## 2. Aplicação do Desconto
O crédito calculado é aplicado em dois momentos:

### Na Criação da Preferência (Checkout)
A `CriarPreferenciaCheckout` subtrai o crédito do valor total do novo plano. O Mercado Pago recebe apenas o valor líquido a pagar.

### No Processamento do Pagamento
A `ProcessarPagamentoCheckout` verifica se, após aplicar os créditos, o valor a pagar é **zero ou negativo**.

## 3. Upgrade Gratuito (Custo Zero)
Se o crédito do usuário for maior ou igual ao preço do novo plano:
- O sistema **não** abre o Mercado Pago.
- Um botão "Confirmar Upgrade Grátis" aparece no frontend.
- Ao clicar, o sistema simula uma aprovação e ativa o novo plano instantaneamente através do `AtivarPlanoUsuario`.

## 4. Lógica de Vencimento
Implementada no `AtivarPlanoUsuario`:

- **Mesmo Plano (Renovação)**: Os novos dias são **somados** à data de término atual. (Ex: Se vencia dia 10 e comprou mais 30 dias, agora vence dia 10 do mês seguinte).
- **Novo Plano (Upgrade)**: A assinatura antiga é cancelada e a nova começa a valer **a partir de hoje**. O valor que "sobraria" da antiga já foi descontado como crédito no momento da compra.

## 5. Histórico de Pagamentos
O histórico (`HistoricoPagamento`) agora armazena:
- O valor bruto do plano.
- O valor dos créditos de prorrata utilizados.
- O valor real pago.
- O período (Mensal/Anual) vinculado à assinatura para exibição clara no perfil.

# Changelog — Sessão 21/02/2026

## 💱 Sistema de Moedas Completo

### `useMoney.js` (composable novo)
- Busca cotações **reais** via `open.er-api.com` (API gratuita, sem chave) com cache em memória
- Fallback hardcoded: `1 BRL → USD 0.18 | EUR 0.17 | GBP 0.14`
- Funções expostas: `fromBRL(value)`, `toBRL(value)`, `formatMoney()`, `currencySymbol`, `formatNumber()`
- **Base sempre BRL** — banco armazena em BRL, exibição converte no front

### `CoinSelector.vue`
- EUR 🇪🇺 e GBP 🇬🇧 adicionados
- Fonte reduzida, dropdown com bandeirinha + nome completo

### Views atualizadas com `useMoney`
| View/Componente | O que mudou |
|---|---|
| `Dashboard.vue` | Receita, despesa, saldo e tooltips do gráfico convertidos via `fromBRL` |
| `Lancamentos.vue` | Valores na tabela + símbolo correto |
| `Metas.vue` | `formatPrice` usa `fromBRL` + locale correto |
| `Plans.vue` | Preços dos planos convertidos (planos armazenados em BRL) |
| `DateInput.vue` | Locale do picker (pt-BR/en-US/etc) vem de `currencyMeta.locale` |

---

## 📅 Bug de Data (UTC Offset -1 dia)

**Causa:** `new Date('2025-12-31')` é interpretado como UTC midnight → em fuso -3 vira 30/12.

**Fix aplicado em todos os locais:**
```js
// ANTES (errado)
new Date(date).toLocaleDateString(locale)

// DEPOIS (correto)
const [y, m, d] = date.split('T')[0].split('-').map(Number)
new Date(y, m - 1, d).toLocaleDateString(locale)  // construtor local, sem UTC
```

Arquivos corrigidos: `Metas.vue`, `Lembretes.vue` (`formatDate`, `formatDateShort`, `openDialogWithDate`) e `DateInput.vue` (`parseValue` já usa `+ 'T00:00:00'`).

---

## 🗓️ Calendário FullCalendar

### Substituição do VCalendar
- Instalado: `@fullcalendar/vue3`, `@fullcalendar/daygrid`, `@fullcalendar/timegrid`, `@fullcalendar/list`, `@fullcalendar/interaction`
- Vistas: **Mês / Semana / Lista** (como Google Calendar)
- Locale: `pt-br` nativo do FullCalendar
- Eventos do banco aparecem com emoji + cor personalizada

### Comportamento de eventos vencidos
- Eventos **vencidos** (data+hora já passou) → exibidos com `opacity: 0.55` e leve grayscale
- Eventos **concluídos** → exibidos com tom verde suave
- Eventos vencidos **não são removidos** automaticamente — continuam visíveis para histórico. Para removê-los do calendário, mude o status para `inativo` ou `concluido`.

### Interações
- Clique em **dia vazio** → abre modal para novo lembrete com data pré-preenchida
- Clique em **evento** → abre edição do lembrete
- Seleção de **intervalo** (drag) → cria lembrete com data inicial

---

## ⚠️ Notas importantes

- **Plans.vue mostra preços convertidos** mas o pagamento ao Mercado Pago é **sempre em BRL** (requisito da plataforma). A conversão é só visual.
- O build produz chunks grandes no `Lancamentos` (XLSX) e `Lembretes` (FullCalendar). Não impacta funcionamento, apenas warning de tamanho.

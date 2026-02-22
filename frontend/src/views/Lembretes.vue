<template>
  <v-container>
    <v-row align="center" class="mb-6 pt-4">
      <v-col cols="12">
        <div class="d-flex flex-column gap-6">
          <!-- Top Row: Title & New Button -->
          <div class="d-flex flex-column flex-md-row justify-space-between align-md-center gap-4">
            <div class="d-flex align-center">
              <v-avatar color="secondary" size="48" class="mr-3 elevation-2">
                <v-icon icon="mdi-calendar-clock" color="white"></v-icon>
              </v-avatar>
              <div>
                <h1 class="text-h4 font-weight-bold">{{ $t('sidebar.reminders') }}</h1>
                <p class="text-subtitle-2 text-medium-emphasis">{{ $t('metas.lembretes_subtitle') }}</p>
              </div>
            </div>

            <div class="d-flex flex-wrap gap-2 w-100 justify-sm-end">
                <v-btn 
                  :variant="viewMode === 'list' ? 'elevated' : 'text'"
                  :color="viewMode === 'list' ? 'secondary' : ''"
                  icon="mdi-format-list-bulleted"
                  @click="viewMode = 'list'"
                ></v-btn>
                <v-btn 
                  :variant="viewMode === 'calendar' ? 'elevated' : 'text'"
                  :color="viewMode === 'calendar' ? 'secondary' : ''"
                  icon="mdi-calendar-month"
                  @click="viewMode = 'calendar'"
                ></v-btn>
                <v-btn 
                  color="info" 
                  variant="tonal"
                  @click="testNotification"
                  icon
                >
                  <v-icon icon="mdi-bell-ring-outline"></v-icon>
                  <v-tooltip activator="parent" location="bottom">{{ $t('metas.test_notifications') }}</v-tooltip>
                </v-btn>
                <v-btn 
                  color="secondary" 
                  prepend-icon="mdi-note-plus-outline" 
                  rounded="xl" 
                  size="large"
                  class="font-weight-bold px-8 elevation-3 shadow-secondary flex-grow-1 flex-sm-grow-0"
                  @click="openDialog"
                >
                  {{ $t('metas.new_note') }}
                </v-btn>
            </div>
          </div>

          <!-- Bottom Row: Status Filters -->
          <div v-if="viewMode === 'list'" class="filter-wrapper d-flex justify-center justify-md-start">
            <v-btn-toggle
              v-model="statusFilter"
              mandatory
              color="secondary"
              variant="text"
              class="status-toggle-group gap-2 flex-wrap justify-center justify-md-start"
            >
              <v-btn value="andamento" class="rounded-pill filter-pill" size="small">
                <v-icon start size="18">mdi-progress-clock</v-icon>
                {{ $t('metas.filter.active') }}
              </v-btn>
              <v-btn value="concluido" class="rounded-pill filter-pill" size="small">
                <v-icon start size="18">mdi-check-circle</v-icon>
                {{ $t('metas.filter.completed') }}
              </v-btn>
              <v-btn value="inativo" class="rounded-pill filter-pill" size="small">
                <v-icon start size="18">mdi-pause-circle</v-icon>
                {{ $t('metas.filter.inactive') }}
              </v-btn>
              <v-btn value="all" class="rounded-pill filter-pill" size="small">
                <v-icon start size="18">mdi-format-list-bulleted</v-icon>
                {{ $t('metas.filter.all') }}
              </v-btn>
            </v-btn-toggle>
          </div>
        </div>
      </v-col>
    </v-row>

    <div v-if="loading" class="d-flex flex-column align-center py-12">
      <v-skeleton-loader type="card" class="mb-6" :loading="true" :width="'100%'" :height="120" />
      <v-skeleton-loader type="card" class="mb-6" :loading="true" :width="'100%'" :height="120" />
    </div>

    <template v-else>
      <!-- List View -->
      <v-row v-if="viewMode === 'list' && filteredNotes.length">
        <v-col v-for="note in filteredNotes" :key="note.id" cols="12" sm="6" md="4" lg="3">
          <v-card 
            class="rounded-xl goal-card notepad-card overflow-hidden" 
            :class="{ 'completed-note': note.status === 'concluido' }"
            elevation="2"
          >
            <div class="notepad-header" :style="`background: ${note.cor || '#FFEB3B'}`"></div>
            <v-card-text class="pa-5 notepad-content">
              <div class="d-flex align-start mb-2">
                <span v-if="note.icone" class="mr-2 notepad-emoji">{{ note.icone }}</span>
                <span class="text-h6 font-weight-bold notepad-title text-truncate">{{ note.titulo }}</span>
              </div>

              <div class="notepad-text mb-4">
                  {{ note.descricao || $t('metas.no_notes_content') }}
              </div>

              <div class="d-flex justify-space-between align-center text-caption text-medium-emphasis mt-auto">
                <span v-if="note.prazo" class="d-flex align-center">
                  <v-icon icon="mdi-calendar-clock" size="14" class="mr-1" :color="isToday(note.prazo) ? 'error' : ''"></v-icon>
                  <span :class="{'text-error font-weight-bold': isToday(note.prazo)}">{{ formatDate(note.prazo) }}</span>
                  <span v-if="note.hora" class="ml-2">
                    <v-icon icon="mdi-clock-outline" size="12" class="mr-1"></v-icon>
                    {{ formatTime(note.hora) }}
                  </span>
                  <v-tooltip v-if="isToday(note.prazo)" activator="parent" location="top">Lembrete para hoje!</v-tooltip>
                </span>
                <v-chip v-if="note.status === 'concluido'" size="x-small" color="success" variant="tonal" class="rounded-lg">{{ $t('metas.completed') }}</v-chip>
            </div>
            </v-card-text>

            <v-divider opacity="0.1"></v-divider>
            <v-card-actions class="pa-2 px-4 d-flex flex-wrap justify-end gap-1">
              <v-btn 
                :icon="note.status === 'concluido' ? 'mdi-refresh' : 'mdi-check'" 
                size="small" 
                variant="text" 
                :color="note.status === 'concluido' ? 'warning' : 'success'" 
                @click="toggleStatusConcluido(note)"
              ></v-btn>
              <v-btn 
                v-if="note.status === 'inativo'"
                variant="text" 
                size="small" 
                color="info" 
                @click="reativarItem(note)"
                icon="mdi-restore"
              ></v-btn>
              <v-btn v-if="note.status !== 'inativo'" icon="mdi-pencil-outline" size="small" variant="text" color="primary" @click="editNote(note)"></v-btn>
              <v-btn v-if="note.status !== 'inativo'" icon="mdi-delete-outline" size="small" variant="text" color="error" @click="confirmDelete(note)"></v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>

      <!-- FullCalendar View -->
      <div v-else-if="viewMode === 'calendar'" class="fullcalendar-wrapper mt-2">
        <v-card class="rounded-xl overflow-hidden fc-card" elevation="2">
          <FullCalendar :options="calendarOptions" ref="fcRef" />
        </v-card>
      </div>

      <div v-else-if="viewMode === 'list'" class="text-center py-12 text-medium-emphasis">
        <v-icon icon="mdi-calendar-blank-outline" size="64" class="mb-4 opacity-20"></v-icon>
        <p>{{ $t('metas.empty_filter') }}</p>
      </div>
    </template>
    
    <ModalMeta v-model="dialog" :meta="itemAEditar" initialTipo="pessoal" @saved="onNoteSaved" />
    <ModalExcluirMeta
      v-model="deleteDialog"
      :meta="noteToDelete"
      resourceType="lembretes"
      @deleted="onNoteDeleted"
      @rollback="({ id, oldStatus }) => { const i = anotacoes.value.findIndex(a => a.id === id); if(i !== -1) anotacoes.value[i].status = oldStatus; fetchNotes(true) }"
    />

    <!-- Day Details Dialog -->
    <v-dialog v-model="dayDetailsDialog" maxWidth="500px">
        <v-card class="rounded-xl pa-4">
            <v-card-title class="d-flex justify-space-between align-center">
                <span class="font-weight-bold">{{ $t('metas.day_details', { date: formatDateShort(calendarDate) }) }}</span>
                <v-btn icon="mdi-close" variant="text" @click="dayDetailsDialog = false"></v-btn>
            </v-card-title>
            <v-card-text>
                <div v-if="notesForCalendarDate.length">
                    <v-list class="bg-transparent">
                        <v-list-item 
                          v-for="note in notesForCalendarDate" 
                          :key="note.id"
                          class="mb-3 rounded-xl bg-surface-variant elevation-0 border-s-xl"
                          :style="`border-left-color: ${note.cor || '#FFEB3B'} !important`"
                          @click="editNote(note); dayDetailsDialog = false"
                        >
                          <template v-slot:prepend>
                              <span class="text-h5 mr-3">{{ note.icone || '📝' }}</span>
                          </template>
                          <v-list-item-title class="font-weight-bold">
                              {{ note.titulo }}
                              <span v-if="note.hora" class="text-caption ml-2">
                                  <v-icon icon="mdi-clock-outline" size="12"></v-icon> {{ formatTime(note.hora) }}
                              </span>
                          </v-list-item-title>
                          <v-list-item-subtitle>{{ note.descricao }}</v-list-item-subtitle>
                        </v-list-item>
                    </v-list>
                </div>
                <div v-else class="text-center py-6">
                    <p class="text-medium-emphasis mb-4">{{ $t('metas.no_calendar_notes') }}</p>
                </div>
                <v-btn block color="secondary" rounded="lg" @click="openDialogWithDate(calendarDate); dayDetailsDialog = false">
                    {{ $t('metas.add_event') }}
                </v-btn>
            </v-card-text>
        </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useAuthStore } from '../stores/auth'
import { toast } from 'vue3-toastify'
import ModalMeta from '../components/Modals/Metas/ModalMeta.vue'
import ModalExcluirMeta from '../components/Modals/Metas/ModalExcluirMeta.vue'
import { useI18n } from 'vue-i18n'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'
import interactionPlugin from '@fullcalendar/interaction'

const { t } = useI18n()
const authStore = useAuthStore()

const anotacoes = ref([])
const loading = ref(false)
const dialog = ref(false)
const deleteDialog = ref(false)
const dayDetailsDialog = ref(false)
const noteToDelete = ref(null)
const itemAEditar = ref(null)
const fcRef = ref(null)

// UI State
const statusFilter = ref('andamento')
const viewMode = ref('calendar')
const calendarDate = ref(new Date())

onMounted(() => {
  fetchNotes()
})

const onNoteSaved = (optimisticItem) => {
    if (optimisticItem) {
        const index = anotacoes.value.findIndex(a => a.id === optimisticItem.id)
        if (index !== -1) {
            anotacoes.value[index] = { ...anotacoes.value[index], ...optimisticItem }
        } else {
            anotacoes.value.unshift(optimisticItem)
        }
    }
    setTimeout(() => { fetchNotes(true) }, 800)
}

const onNoteDeleted = ({ id }) => {
    const index = anotacoes.value.findIndex(a => a.id === id)
    if (index !== -1) {
        anotacoes.value[index].status = 'inativo'
    }
    setTimeout(() => { fetchNotes(true) }, 1500)
}

const fetchNotes = async (isSilent = false) => {
  if (!isSilent) loading.value = true
  try {
    const response = await authStore.apiFetch('/lembretes')
    if (response.ok) anotacoes.value = await response.json()
  } catch (e) {
    console.error(e)
  } finally {
    if (!isSilent) loading.value = false
  }
}

const filteredNotes = computed(() => {
  if (statusFilter.value === 'all') return anotacoes.value
  if (statusFilter.value === 'andamento') {
    return anotacoes.value.filter(n => (n.status === 'andamento' || n.status === 'atrasado') && n.status !== 'inativo')
  }
  return anotacoes.value.filter(n => n.status === statusFilter.value)
})

// Calendar Logic
const notesForCalendarDate = computed(() => {
    if (!calendarDate.value) return []
    const d = new Date(calendarDate.value)
    const selected = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`
    
    return anotacoes.value.filter(n => {
        if (!n.prazo || n.status === 'inativo') return false
        const nDate = typeof n.prazo === 'string' ? n.prazo.split('T')[0] : null
        return nDate === selected
    })
})

const calendarAttributes = computed(() => {
    return anotacoes.value
        .filter(n => n.prazo && n.status !== 'inativo')
        .map(n => {
            // Guaranteeing YYYY-MM-DD format for V-Calendar
            const dateStr = typeof n.prazo === 'string' ? n.prazo.split('T')[0] : null
            if (!dateStr) return null
            return {
                key: `note-${n.id}`,
                dot: { color: n.status === 'concluido' ? 'green' : 'blue' },
                dates: dateStr,
                popover: { label: n.titulo }
            }
        }).filter(attr => attr !== null)
})

const calendarOptions = computed(() => ({
  plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
  initialView: 'dayGridMonth',
  locale: t('common.currency') === 'R$' ? 'pt-br' : 'en',
  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    right: 'dayGridMonth,timeGridWeek,listMonth'
  },
  buttonText: {
    today: t('metas.calendar.today'),
    month: t('metas.calendar.month'),
    week: t('metas.calendar.week'),
    list: t('metas.calendar.list')
  },
  height: 'auto',
  selectable: true,
  editable: false,
  dayMaxEvents: 3,
  fixedWeekCount: false,
  eventTimeFormat: { hour: '2-digit', minute: '2-digit', meridiem: false },
  events: anotacoes.value
    .filter(n => n.prazo && n.status !== 'inativo')
    .map(n => {
      // Fix: parse date string as local date (YYYY-MM-DD) to avoid UTC shift
      const dateStr = typeof n.prazo === 'string' ? n.prazo.split('T')[0] : null
      if (!dateStr) return null
      return {
        id: String(n.id),
        title: (n.icone ? n.icone + ' ' : '') + n.titulo,
        start: dateStr + (n.hora ? 'T' + n.hora : ''),
        allDay: !n.hora,
        backgroundColor: n.cor || (n.status === 'concluido' ? '#38ef7d' : '#1867C0'),
        borderColor: 'transparent',
        textColor: '#fff',
        extendedProps: { note: n },
        // Mark overdue events so CSS can style them
        classNames: (() => {
          if (n.status === 'concluido') return ['fc-event-done']
          const eventDate = n.hora ? new Date(dateStr + 'T' + n.hora) : new Date(dateStr + 'T23:59:59')
          return eventDate < new Date() ? ['fc-event-overdue'] : []
        })()
      }
    }).filter(Boolean),
  select: (info) => {
    // Click on empty day → open new note dialog
    openDialogWithDate(info.startStr)
  },
  eventClick: (info) => {
    const note = info.event.extendedProps.note
    if (note) editNote(note)
  },
  dayCellClassNames: () => ['fc-day-custom'],
}))

const openDialog = () => {
  itemAEditar.value = { tipo: 'pessoal' }
  dialog.value = true
}

const onDateClick = (day) => {
    if (day && day.date) {
        calendarDate.value = day.date
    }
    dayDetailsDialog.value = true
}

const testNotification = () => {
    toast.info(t('metas.toast_test_notification'), {
        autoClose: 6000,
        position: 'top-right'
    })
}

const openDialogWithDate = (date) => {
    // Accept either a YYYY-MM-DD string (from FullCalendar) or a Date object
    let prazo = null
    if (date) {
        if (typeof date === 'string') {
            prazo = date.split('T')[0]  // already YYYY-MM-DD, no UTC conversion
        } else {
            // Date object: use local date parts to avoid UTC offset
            const d = date instanceof Date ? date : new Date(date)
            prazo = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`
        }
    }
    itemAEditar.value = { tipo: 'pessoal', prazo }
    dialog.value = true
}

const editNote = (note) => {
  itemAEditar.value = note
  dialog.value = true
}

const confirmDelete = (note) => {
  noteToDelete.value = note
  deleteDialog.value = true
}

const toggleStatusConcluido = async (item) => {
  const endpoint = `/lembretes/${item.id}`
  const oldStatus = item.status
  const newStatus = item.status === 'concluido' ? 'andamento' : 'concluido'
  
  item.status = newStatus
  toast.success(newStatus === 'concluido' ? t('toasts.success_update') : t('toasts.success_restore'))

  try {
    const response = await authStore.apiFetch(endpoint, {
      method: 'PATCH',
      body: JSON.stringify({ status: newStatus })
    })
    if (!response.ok) throw new Error('Erro ao atualizar status')
  } catch (e) { 
      item.status = oldStatus
      console.error(e) 
  } finally {
      setTimeout(() => { fetchNotes(true) }, 800)
  }
}

const reativarItem = async (item) => {
  const endpoint = `/lembretes/${item.id}/reativar`
  const oldStatus = item.status
  item.status = 'andamento'
  toast.success(t('metas.actions.reactivate_success'))
  
  try {
    const response = await authStore.apiFetch(endpoint, { method: 'POST' })
    if (!response.ok) throw new Error('Erro ao reativar')
  } catch (e) { 
      item.status = oldStatus
      console.error(e) 
  } finally {
      setTimeout(() => { fetchNotes(true) }, 800)
  }
}

const formatDate = (date) => {
    if (!date) return ''
    const locale = t('common.currency') === 'R$' ? 'pt-BR' : 'en-US'
    
    // Fix for the -1 day offset bug
    const dStr = typeof date === 'string' ? date.split('T')[0] : null
    if (dStr && dStr.includes('-')) {
        const [y, m, d] = dStr.split('-').map(Number)
        return new Date(y, m - 1, d).toLocaleDateString(locale)
    }
    
    return new Date(date).toLocaleDateString(locale)
}

const formatDateShort = (date) => {
    if (!date) return ''
    const locale = t('common.currency') === 'R$' ? 'pt-BR' : 'en-US'
    
    // Fix for the -1 day offset bug
    const dStr = typeof date === 'string' ? date.split('T')[0] : null
    if (dStr && dStr.includes('-')) {
        const [y, m, d] = dStr.split('-').map(Number)
        return new Date(y, m - 1, d).toLocaleDateString(locale, { day: 'numeric', month: 'long' })
    }

    return new Date(date).toLocaleDateString(locale, { day: 'numeric', month: 'long' })
}

const formatTime = (time) => {
    if (!time) return ''
    return time.substring(0, 5) // HH:mm
}

const isToday = (date) => {
    if (!date) return false
    const dStr = typeof date === 'string' ? date.split('T')[0] : null
    const todayStr = new Date().toISOString().split('T')[0]
    return dStr === todayStr
}
</script>

<style scoped>
.goal-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border: 1px solid rgba(var(--v-border-color), 0.1);
}

.goal-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 20px rgba(0,0,0,0.1) !important;
}

.notepad-card {
  background: #FFF9C4;
  position: relative;
  min-height: 200px;
  display: flex;
  flex-direction: column;
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  border: 1px solid rgba(0, 0, 0, 0.05);
}

.notepad-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1) !important;
}

:root[data-v-theme="dark"] .notepad-card,
.v-theme--dark .notepad-card {
  background: #33301a;
}

.completed-note {
  opacity: 0.7;
}

.notepad-header {
  height: 12px;
  width: 100%;
}

.notepad-content {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
}

.notepad-emoji {
  font-size: 1.5rem;
}

.notepad-title {
  color: inherit;
  font-family: 'Inter', sans-serif;
}

.notepad-text {
  color: inherit;
  opacity: 0.9;
  font-size: 0.95rem;
  line-height: 1.5;
  white-space: pre-wrap;
  font-family: 'Inter', sans-serif;
}

.gap-1 { gap: 8px; }
.gap-2 { gap: 8px; }
.gap-4 { gap: 16px; }
.gap-6 { gap: 24px; }

.status-toggle-group {
    background: transparent !important;
}

.filter-pill {
    background: rgba(var(--v-theme-surface), 0.5) !important;
    backdrop-filter: blur(4px);
    border: 1px solid rgba(var(--v-border-color), 0.1) !important;
    text-transform: none !important;
    font-weight: 600 !important;
    letter-spacing: 0 !important;
    transition: all 0.2s ease;
    opacity: 0.8;
}

.filter-pill.v-btn--active {
    background: rgb(var(--v-theme-secondary)) !important;
    color: white !important;
    opacity: 1;
    border-color: transparent !important;
    box-shadow: 0 4px 10px rgba(var(--v-theme-secondary), 0.3) !important;
}

.shadow-secondary {
  box-shadow: 0 8px 20px rgba(var(--v-theme-secondary), 0.3) !important;
}

.text-error {
    color: rgb(var(--v-theme-error)) !important;
}

/* ─── FullCalendar custom theme ──────────────────────────────── */
.fullcalendar-wrapper {
  width: 100%;
}

.fc-card {
  padding: 0 !important;
}

/* Toolbar */
:deep(.fc .fc-toolbar.fc-header-toolbar) {
  padding: 16px 20px 12px;
  margin-bottom: 0;
  flex-wrap: wrap;
  gap: 8px;
}

:deep(.fc .fc-toolbar-title) {
  font-size: 1.15rem !important;
  font-weight: 700 !important;
  text-transform: capitalize;
}

:deep(.fc .fc-button) {
  background: transparent !important;
  border: 1px solid rgba(var(--v-border-color), 0.3) !important;
  color: rgb(var(--v-theme-on-surface)) !important;
  border-radius: 8px !important;
  padding: 4px 12px !important;
  font-family: 'Inter', sans-serif !important;
  font-size: 0.82rem !important;
  font-weight: 600 !important;
  box-shadow: none !important;
  transition: all 0.2s ease !important;
}

:deep(.fc .fc-button:hover),
:deep(.fc .fc-button-active) {
  background: rgb(var(--v-theme-secondary)) !important;
  border-color: rgb(var(--v-theme-secondary)) !important;
  color: white !important;
}

:deep(.fc .fc-button-primary:not(:disabled).fc-button-active) {
  background: rgb(var(--v-theme-secondary)) !important;
  border-color: rgb(var(--v-theme-secondary)) !important;
  color: white !important;
}

/* Day header */
:deep(.fc .fc-col-header-cell-cushion) {
  font-size: 0.78rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  padding: 8px 4px;
  color: rgb(var(--v-theme-on-surface));
  opacity: 0.7;
  text-decoration: none !important;
}

/* Day number */
:deep(.fc .fc-daygrid-day-number) {
  font-size: 0.85rem;
  font-weight: 600;
  padding: 6px 10px;
  color: rgb(var(--v-theme-on-surface));
  text-decoration: none !important;
}

/* Today highlight */
:deep(.fc .fc-day-today) {
  background: rgba(var(--v-theme-secondary), 0.08) !important;
}

:deep(.fc .fc-day-today .fc-daygrid-day-number) {
  background: rgb(var(--v-theme-secondary));
  color: white;
  border-radius: 50%;
  width: 28px;
  height: 28px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 4px;
}

/* Events */
:deep(.fc .fc-event) {
  border-radius: 6px !important;
  padding: 2px 6px !important;
  font-size: 0.78rem !important;
  font-weight: 600 !important;
  cursor: pointer !important;
  transition: opacity 0.2s ease;
}

:deep(.fc .fc-event:hover) {
  opacity: 0.85;
  transform: scale(1.02);
}

:deep(.fc .fc-event-title) {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Grid lines */
:deep(.fc .fc-scrollgrid) {
  border: none !important;
}

:deep(.fc .fc-scrollgrid td),
:deep(.fc .fc-scrollgrid th) {
  border-color: rgba(var(--v-border-color), 0.12) !important;
}

/* More link */
:deep(.fc .fc-more-link) {
  color: rgb(var(--v-theme-secondary)) !important;
  font-weight: 700 !important;
  font-size: 0.75rem !important;
}

/* List view */
:deep(.fc .fc-list-event-title a) {
  color: rgb(var(--v-theme-on-surface)) !important;
  text-decoration: none;
  font-weight: 600;
}

:deep(.fc .fc-list-day-cushion) {
  background: rgba(var(--v-theme-secondary), 0.1) !important;
}

:deep(.fc .fc-list-day-text),
:deep(.fc .fc-list-day-side-text) {
  font-weight: 700 !important;
  text-decoration: none !important;
}

/* Remove hover highlight na view lista (ocultava o texto) */
:deep(.fc .fc-list-event:hover td) {
  background: transparent !important;
  cursor: pointer;
}

/* FullCalendar Dark Mode Fixes */
:deep(.fc) {
  --fc-button-text-color: #fff;
  --fc-button-bg-color: #1867C0;
  --fc-button-border-color: #1867C0;
  --fc-button-hover-bg-color: #1557a0;
  --fc-button-hover-border-color: #1557a0;
  --fc-button-active-bg-color: #10457d;
  --fc-button-active-border-color: #10457d;
}

:deep(.v-theme--dark .fc .fc-col-header-cell) {
    background-color: #2a2a2a !important;
}

:deep(.v-theme--dark .fc .fc-col-header-cell-cushion) {
    color: #fff !important;
}

:deep(.v-theme--dark .fc .fc-list-day-cushion) {
    background-color: #2a2a2a !important;
}

:deep(.v-theme--dark .fc .fc-toolbar-title) {
    color: #5CBBF6 !important;
}

:deep(.fc .fc-toolbar-title) {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1867C0;
}

:deep(.fc .fc-button-primary:disabled) {
    background-color: #1867c0;
    border-color: #1867c0;
    opacity: 0.65;
}

:deep(.fc-theme-standard td, .fc-theme-standard th) {
    border-color: rgba(var(--v-border-color), 0.1);
}
</style>

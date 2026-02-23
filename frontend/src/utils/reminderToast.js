import { toast } from 'vue3-toastify'

function daysUntil(date) {
  const today = new Date()
  const diff = new Date(date).getTime() - today.getTime()
  return Math.ceil(diff / (1000 * 60 * 60 * 24))
}

export function showReminderToast(reminder) {
  const date = new Date(reminder.data_aviso || reminder.date)
  const days = daysUntil(date)

  const dayLabel = days === 0
    ? 'Hoje'
    : days === 1
      ? 'Amanhã'
      : `Faltam ${days} dias`

  const message = `📅 ${date.toLocaleDateString('pt-BR')} — ${dayLabel}\n📝 ${reminder.titulo || reminder.title}`

  toast.info(message, {
    autoClose: 6000,
    position: 'top-right'
  })
}

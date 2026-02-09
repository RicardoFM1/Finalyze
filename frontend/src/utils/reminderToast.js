import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

function daysUntil(date) {
  const today = new Date()
  const diff = date.getTime() - today.getTime()
  return Math.ceil(diff / (1000 * 60 * 60 * 24))
}

export function showReminderToast(reminder) {
  const days = daysUntil(reminder.date)

  const message = `
📅 ${reminder.date.toLocaleDateString('pt-BR')}
📝 ${reminder.title}
⏳ Faltam ${days} dias
  `

  toast.info(message, {
    autoClose: 6000,
    position: 'top-right'
  })
}

import { toast } from "vue3-toastify";
import ReminderToast from "../components/ReminderToast/ReminderToast.vue";
import { Title } from "chart.js";

export function showReminderToast(reminder) {
    toast(
        {
        component: ReminderToast,
        props: { reminder}   
        },
        {
            autoClose: 6000,
            closeOnClick: true
        }
    )
}

const reminder = {
    title: '💰 Pagar cartão de crédito',
    date: '2026-02-19'
}
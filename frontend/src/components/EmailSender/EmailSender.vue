<template>
    <v-card class="pa-4 ">
    <v-card-title>{{ title }}</v-card-title>

    <v-card-text>
        <v-text-field
            v-model="email"
            label="Enviar para"
            variant="outlined"
            :disabled="loading"
        />

        <v-text-field
            v-model="subject"
            label="Assunto"
            variant="outlined"
            :disabled="loading"
        />
        
        <v-textarea
            v-model="message"
            label="Mensagem"
            rows="4"
            variant="outlined"
            :disabled="loading"
        />

        <v-card-actions>
        <v-btn
            color="primary"
            :loading="loading"
            @click="sendEmail"
        >Enviar Email
        </v-btn>
    </v-card-actions>
    </v-card-text>
    </v-card>
</template>

<script setup>
import { ref } from 'vue';
import { toast } from 'vue3-toastify';
import { useAuthStore } from '../../stores/auth'



const props = defineProps({
    title: {
        type: String,
        default: 'Enviar Email',
    },
    defaultEmail: String,
    defaultSubject: String,
    defaultMessage: String,
})

const emit = defineEmits(['sent']);

const autnStore = useAuthStore();

const email = ref(props.defaultEmail || '');
const subject = ref(props.defaultSubject || '');
const message = ref(props.defaultMessage || '');

const sendEmail = async () => {
    loading.value = true;
    try {
       const response = await fetch('/api/send-email', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${autnStore.token}`,
        },
        body: JSON.stringify({
            email: email.value,
            subject: subject.value,
            message: message.value,
        })
       })

       if (!response.ok) {
        throw new Error('Failed to send email');
       }

       toast.success('Email enviado com sucesso!');
         emit('sent');
    } catch (error) {
        toast.error('Erro ao enviar email: ' + error.message);
    } finally {
        loading.value = false;
    }
}
</script>
<template>
    <div class="reminder-toast">
        <div class="header">
            <span class="date">📅 {{ formattedDate }}</span>
            <span class="days">
                ⏳ {{ daysLeft }} dia{{ daysLeft > 1 ? 's' : '' }}
            </span>
        </div>

        <div class="title">
            {{ reminder.title }}
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { differenceInDays, startOfDay} from 'date-fns'

const props = defineProps({
    type:Object,
    require: true
})
</script>

<style scoped>
.reminder-toast{
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.header {
    display: flex;
    justify-content: space-between;
    font-size: 0.85rem;
    opacity: 0.8;
}

.title {
    font-weight: 600;
    font-size: 0.95rem;
}
</style>
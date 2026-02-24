<template>
  <v-select
    v-model="selectedLanguage"
    :items="languages"
    item-title="label"
    item-value="value"
    :label="$t('landing.select_language')"
    variant="outlined"
    density="compact"
    hide-details
    style="width: 100%;"
  ></v-select>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { useAuthStore } from '../../stores/auth'

const authStore = useAuthStore();
const { locale } = useI18n();

const languages = [
  { label: 'Português (BR)', value: 'pt' },
  { label: 'English (US)', value: 'en' },
];

const selectedLanguage = ref(locale.value);

watch(selectedLanguage, (newLang) => {
  authStore.setLanguage(newLang);
});

watch(locale, (newLocale) => {
    selectedLanguage.value = newLocale;
});
</script>

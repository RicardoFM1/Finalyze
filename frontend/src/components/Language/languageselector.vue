<template>
  <v-select
    v-model="selectedLanguage"
    :items="languages"
    item-title="label"
    item-value="value"
    :label="$t('landing.features.select_language')"
    variant="outlined"
    density="compact"
    hide-details
    style="max-width: 200px;"
  ></v-select>
</template>

<script setup>
import { useI18n } from 'vue-i18n';
import { ref, watch } from 'vue';

const { locale } = useI18n();

const languages = [
  { label: 'Português (BR)', value: 'pt' },
  { label: 'English (US)', value: 'en' },
];

const selectedLanguage = ref(locale.value);

watch(selectedLanguage, (newLang) => {
  locale.value = newLang;
});

watch(locale, (newLocale) => {
    selectedLanguage.value = newLocale;
});
</script>

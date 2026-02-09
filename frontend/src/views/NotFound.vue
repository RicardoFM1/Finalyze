<template>
  <v-container class="text-center mt-12">
    <h1 class="text-h2 font-weight-bold">{{ $t('NotFound.title') }}</h1>
    <p>{{ $t('NotFound.p') }}</p>
    <v-btn color="primary" @click="$router.push('/')">{{ $t('NotFound.btn_home') }}</v-btn>
  </v-container>
</template>


<script>
import { useRouter} from 'vue-router'
</script>
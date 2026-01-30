<template>
  <v-container class="text-center mt-12">
<<<<<<< HEAD
    <h1 class="text-h2 font-weight-bold">{{ $t('NotFound.title') }}</h1>
    <p>{{ $t('NotFound.p') }}</p>
    <v-btn color="primary" @click="$router.push('/')">{{ $t('NotFound.btn_home') }}</v-btn>
=======
    <h1 class="text-h2 font-weight-bold">404</h1>
    <p>Página não encontrada ou usuário sem permissão</p>
    <v-btn color="primary" @click="$router.push('/')">Voltar para a página inicial</v-btn>
>>>>>>> origin/Ricardo
  </v-container>
</template>


<script>
import { useRouter} from 'vue-router'
</script>
<template>
  <v-container class="text-center mt-12">
    <h1 class="text-h2 font-weight-bold">404</h1>
    <p>Página não encontrada ou usuário sem permissão</p>
    <v-btn color="primary" @click="$router.push('/')">Voltar para a página inicial</v-btn>
  </v-container>
</template>


<script>
import { useRouter} from 'vue-router'
</script>
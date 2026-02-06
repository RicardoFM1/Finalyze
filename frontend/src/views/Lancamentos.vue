<template>
  <v-container>
    <v-row class="mb-4" align="center">
      <v-col>
        <h1 class="text-h4 font-weight-bold">Lançamentos</h1>
      </v-col>
      <v-col class="d-flex justify-end">
        <v-btn @click="abrirNovo" color="primary" ><v-icon>mdi-plus</v-icon>Novo lançamento</v-btn>
      </v-col>
    </v-row>

    <v-card elevation="2">
        <v-data-table :headers="headers" :items="lancamentos" :loading="loading">
            <template v-slot:item.data="{ item }">
                {{ new Date(item.data).toLocaleDateString('pt-BR') }}
            </template>
            <template v-slot:item.tipo="{ item }">
                <v-chip :color="item.tipo === 'receita' ? 'success' : 'error'" size="small" class="text-uppercase font-weight-bold">
                    {{ item.tipo === 'receita' ? 'Receita' : 'Despesa' }}
                </v-chip>
            </template>
            <template v-slot:item.valor="{ item }">
                <span :class="item.tipo === 'receita' ? 'text-success' : 'text-error'" class="font-weight-bold">
                    {{ item.tipo === 'receita' ? '+' : '-' }} 
                    {{ new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(item.valor) }}
                </span>
            </template >

          <template v-slot:item.acoes="{ item }">
  <v-menu>
    <template #activator="{ props }">
      <v-btn icon v-bind="props">
        <v-icon>mdi-dots-vertical</v-icon>
      </v-btn>
    </template>

    <v-list>
      <v-list-item @click="abrirEditar(item)">
        <v-list-item-title>Editar</v-list-item-title>
      </v-list-item>

      <v-list-item @click="abrirExcluir(item)">
        <v-list-item-title>Excluir</v-list-item-title>
      </v-list-item>
    </v-list>
  </v-menu>
</template>


        </v-data-table>
    </v-card>
  </v-container>
   <v-dialog v-model="dialogEditar" max-width="500px">
        <v-card>
            <div class="d-flex align-center justify-space-between">
              <v-card-title>Editar Lançamento</v-card-title>
              <v-card-button @click="dialogEditar = !dialogEditar" class="pr-6" style="color: red; cursor: pointer"><v-icon>mdi-close</v-icon></v-card-button>
            </div>
              <v-card-text>
                <v-form @submit.prevent="editar">
                    <v-select v-model="formEdit.tipo" :items="[{title: 'Receita', value: 'receita'}, {title: 'Despesa', value: 'despesa'}]" label="Tipo" required></v-select>
                    <v-text-field v-model="formEdit.valor" label="Valor" prefix="R$" type="number" step="0.01" required></v-text-field>
                    <v-text-field v-model="formEdit.categoria" label="Categoria" required></v-text-field>
                    <v-text-field v-model="formEdit.data" label="Data" type="date" required></v-text-field>
                    <v-text-field v-model="formEdit.descricao" label="Descrição"></v-text-field>
                    <v-btn type="submit" color="primary" block class="mt-4" :loading="loadingEditar">Salvar</v-btn>
                </v-form>
            </v-card-text>
        </v-card>
    </v-dialog>

    <v-dialog v-model="dialogNovo" max-width="500px">
        <v-card>
            <div class="d-flex align-center justify-space-between">
              <v-card-title>Novo Lançamento</v-card-title>
              <v-card-button @click="dialogNovo = !dialogNovo" class="pr-6" style="color: red; cursor: pointer"><v-icon>mdi-close</v-icon></v-card-button>
            </div>
              <v-card-text>
                <v-form @submit.prevent="salvarLancamento">
                    <v-select v-model="form.tipo" :items="[{title: 'Receita', value: 'receita'}, {title: 'Despesa', value: 'despesa'}]" label="Tipo" required></v-select>
                    <v-text-field v-model="form.valor" label="Valor" prefix="R$" type="number" step="0.01" required></v-text-field>
                    <v-text-field v-model="form.categoria" label="Categoria" required></v-text-field>
                    <v-text-field v-model="form.data" label="Data" type="date" required></v-text-field>
                    <v-text-field v-model="form.descricao" label="Descrição"></v-text-field>
                    <v-btn type="submit" color="primary" block class="mt-4" :loading="loadingNovo">Salvar</v-btn>
                </v-form>
            </v-card-text>
        </v-card>
    </v-dialog>

    <v-dialog v-model="dialogExcluir" max-width="420">
  <v-card rounded="xl">
    <v-card-text class="text-center pa-6">
      <v-icon color="error" size="56" class="mb-4">
        mdi-alert-circle-outline
      </v-icon>

      <h3 class="text-h6 font-weight-bold mb-2">
        Confirmar exclusão
      </h3>

      <p class="text-body-2 text-grey-darken-1">
        Tem certeza que deseja excluir este lançamento?
        <br />
        <strong>Essa ação poderá ser desfeita apenas contatando o suporte.</strong>
      </p>
    </v-card-text>

    <v-divider />

   <v-card-actions class="pa-4">
  <v-btn
    variant="outlined"
    class="flex-grow-1"
    @click="dialogExcluir = false"
    :disabled="loadingExcluir"
  >
    Cancelar
  </v-btn>

  <v-btn
    color="error"
    class="flex-grow-1 ml-2"
    :loading="loadingExcluir"
    @click="excluir()"
  >
    Excluir
  </v-btn>
</v-card-actions>

  </v-card>
</v-dialog>

</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '../stores/auth'
import { toast } from 'vue3-toastify'

const authStore = useAuthStore()
const loading = ref(false)
const headers = [
  { title: 'Data', key: 'data', align: 'start' },
  { title: 'Descrição', key: 'descricao', align: 'start' },
  { title: 'Categoria', key: 'categoria', align: 'start' },
  { title: 'Tipo', key: 'tipo', align: 'start' },
  { title: 'Valor', key: 'valor', align: 'end' },
  { title: 'Ações', key: 'acoes', sortable: false, align: 'center'}
]

const form = ref({
    tipo: 'despesa',
    valor: '',
    categoria: '',
    data: new Date().toISOString().substr(0, 10),
    descricao: ''
})

function formatarDataISO(data) {
  return new Date(data).toISOString().slice(0, 10)
}
const formEdit = ref({
  tipo: 'despesa',
  valor: '',
  categoria: '',
  data: '',
  descricao: ''
})
const lancamentos = ref([])
function abrirEditar(item) {
  lancamentoIdEdit.value = item.id

  formEdit.value = {
    tipo: item.tipo,
    valor: item.valor,
    categoria: item.categoria,
    data: formatarDataISO(item.data), 
    descricao: item.descricao
  }

  dialogEditar.value = true
}
const dialogNovo = ref(false)
const abrirNovo = () => {
  dialogNovo.value = true
}
const loadingNovo = ref(false)
const salvarLancamento = async () => {
    loadingNovo.value = true
    try {
        const valor = Number(form.value.valor)
        if (isNaN(valor) || valor <= 0) {
            toast.warning('Por favor, informe um valor válido.')
            loadingNovo.value = false
            return
        }

        const response = await authStore.apiFetch('/lancamentos', {
            method: 'POST',
            body: JSON.stringify({
                ...form.value,
                valor: valor 
            })
        })

        if (response.ok) {
            toast.success('Lançamento adicionado!')
            dialogNovo.value = false
            buscarLancamentos()
           
            form.value = {
                tipo: 'despesa',
                valor: '',
                categoria: '',
                data: new Date().toISOString().substr(0, 10),
                descricao: ''
            }
        } else {
            const data = await response.json().catch(() => ({}))
            toast.error(data.message || 'Erro ao salvar lançamento')
        }
    } catch (e) {
        toast.error('Erro de conexão')
    } finally {
        loadingNovo.value = false
        dialogNovo.value = false
    }
}

function abrirExcluir(item) {
  lancamentoIdExcluir.value = item.id
  dialogExcluir.value = true
}
const buscarLancamentos = async () => {
    loading.value = true
    try {
        const response = await authStore.apiFetch('/lancamentos')
        if (response.ok) {
            lancamentos.value = await response.json()
        }
    } catch (e) {
        console.error(e)
    } finally {
        loading.value = false
    }
}
const loadingEditar = ref(false)
const lancamentoIdEdit = ref(null)
const lancamentoIdExcluir = ref(null)
const dialogEditar = ref(false)
const editar = async () => {
  try{
    loadingEditar.value = true
    const payload = {
      tipo: formEdit.value.tipo,
      valor: formEdit.value.valor,
      categoria: formEdit.value.categoria,
      data: formEdit.value.data,
      descricao: formEdit.value.descricao
    }
    const response = await authStore.apiFetch(`/lancamentos/${lancamentoIdEdit.value}`, {
      method: 'PUT',
      body: JSON.stringify(payload)
    }) 
    if(response.ok){
      toast.success("Sucesso! Lançamento editado com sucesso.")
      dialogEditar.value = false
      buscarLancamentos();
    }else{
      const data = await response.json().catch(() => ({}))
      toast.error(data.message || "Erro ao tentar editar o lançamento.")
    }
  }catch(e){
       toast.error("Erro ao tentar editar o lançamento.")
  }finally{
    loadingEditar.value = false
    dialogEditar.value = false
  }
}
const dialogExcluir = ref(false)
const loadingExcluir = ref(false)
const excluir = async () => {
   try{
    loadingExcluir.value = true
  
    const response = await authStore.apiFetch(`/lancamentos/${lancamentoIdExcluir.value}`, {
      method: 'DELETE'
    }) 
    if(response.ok){
      toast.success("Sucesso! Lançamento deletado com sucesso.")
      buscarLancamentos()
    }else{
      const data = await response.json().catch(() => ({}))
      toast.error(data.message || "Erro ao tentar deletar o lançamento.")
    }
  }catch(e){
       toast.error("Erro ao tentar deletar o lançamento.")
  }finally{
    loadingExcluir.value = false
    dialogExcluir.value = false
  }
}
onMounted(buscarLancamentos)
</script>

<template>
  <Head title="Cajas" />
  <AppLayout>
    <div>
      <template v-if="isLoading">
        <Espera/>
      </template>
      <template v-else>
        <div class="card">
          <addCash @refresh="handleRefresh"/>
          <listCash ref="listCashRef"/>
        </div>
      </template>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import AppLayout from '@/layout/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import Espera from '@/components/Espera.vue';
import addCash from './Desarrollo/addCash.vue';
import listCash from './Desarrollo/listCash.vue';

const isLoading = ref(true);
const listCashRef = ref<InstanceType<typeof listCash> | null>(null);

function handleRefresh() {
  // Refresca la lista de cajas cuando se crean nuevas
  if (listCashRef.value) {
    listCashRef.value.loadCashRegisters();
  }
}

onMounted(() => {
  setTimeout(() => {
    isLoading.value = false;
  }, 1000);
});
</script>
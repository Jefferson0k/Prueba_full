
<template>
  <Head title="Pago Personal" />
  <AppLayout>
    <div>
      <template v-if="isLoading">
        <Espera />
      </template>
      <template v-else>
        <div class="card">
          <AddPagoPersonal @refresh="handleRefresh" />
          <ListPagoPersona ref="listComponent" />
        </div>
      </template>
    </div>
    <ConfirmDialog />
    <Toast />
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import AppLayout from '@/layout/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import Espera from '@/components/Espera.vue';
import AddPagoPersonal from './Desarrollo/addPagoPersonal.vue';
import ListPagoPersona from './Desarrollo/listPagoPersona.vue';
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';

const isLoading = ref(true);
const listComponent = ref();

onMounted(() => {
  setTimeout(() => {
    isLoading.value = false;
  }, 1000);
});

const handleRefresh = () => {
  if (listComponent.value?.loadPagos) {
    listComponent.value.loadPagos();
  }
};
</script>
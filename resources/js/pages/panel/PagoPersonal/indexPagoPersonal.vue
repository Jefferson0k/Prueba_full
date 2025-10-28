<template>
  <Head title="Pago Personal" />
  <AppLayout>
    <div>
      <template v-if="isLoading">
        <Espera />
      </template>
      <template v-else>
        <div class="card">
          <AddPagoPersonal 
            :sucursalSeleccionada="sucursalSeleccionada"
            @refresh="handleRefresh" 
          />
          <ListPagoPersona 
            ref="listComponent"
            v-model:sucursalSeleccionada="sucursalSeleccionada"
            :userSubBranchId="userSubBranchId"
          />
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
import { Head, usePage } from '@inertiajs/vue3';
import Espera from '@/components/Espera.vue';
import AddPagoPersonal from './Desarrollo/addPagoPersonal.vue';
import ListPagoPersona from './Desarrollo/listPagoPersona.vue';
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';

const page = usePage();
const isLoading = ref(true);
const listComponent = ref();

// Obtener la sucursal del usuario autenticado desde Inertia
const userSubBranchId = page.props.auth?.user?.sub_branch_id || null;
const sucursalSeleccionada = ref<string | null>(userSubBranchId);

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
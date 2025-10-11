<template>
  <div>
    <div class="flex items-center justify-between mb-3">
      <h5 class="card-title">Información del Movimiento N° <strong>{{ movement.data?.code }}</strong></h5>
      <div class="flex gap-2">
        <Button label="Regresar" icon="pi pi-arrow-left" @click="goBack" severity="secondary" outlined />
        <Button label="Agregar Producto" icon="pi pi-plus" @click="openDialog" severity="contrast" />
      </div>
    </div>
    <newStockProduct v-model:visible="dialogVisible" :movementId="movement.data?.id"
      @product-added="handleProductAdded" />
    <div class="">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="d-flex flex-wrap mb-3">
          <div class="d-flex align-items-center me-4 mb-2">
            <strong class="font-bold">RUC Proveedor: {{ movement.data?.provider?.ruc }}</strong>
          </div>
          <div class="d-flex align-items-center me-4 mb-2">
            <strong class="font-bold">Tipo Comprobante: {{ movement.data?.voucher_type?.toUpperCase() }}</strong>
          </div>
          <div class="d-flex align-items-center me-4 mb-2">
            <strong class="font-bold">Incluye IGV:
              <Tag :value="movement.data?.includes_igv ? 'SÍ' : 'NO'"
                :severity="movement.data?.includes_igv ? 'success' : 'danger'" class="ml-2" />
            </strong>
          </div>
        </div>
        <div class="d-flex flex-wrap mb-3">
          <div class="d-flex align-items-center me-4 mb-2">
            <strong class="font-bold">Fecha Crédito: {{ movement.data?.credit_date || 'No tiene' }}</strong>
          </div>
          <div class="d-flex align-items-center me-4 mb-2">
            <strong class="font-bold">Razón Social: {{ movement.data?.provider?.razon_social }}</strong>
          </div>
          <div class="d-flex align-items-center me-4 mb-2">
            <strong class="font-bold">Fecha: {{ movement.data?.date }}</strong>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import Tag from 'primevue/tag';
import Button from 'primevue/button';
import newStockProduct from './newStockProduct.vue';
import { useToast } from 'primevue/usetoast';

const toast = useToast();

defineProps<{
  movement: any
}>();

const emit = defineEmits(['reload-details']);

const dialogVisible = ref(false);

const openDialog = () => {
  dialogVisible.value = true;
};

const goBack = () => {
  router.visit('/panel/movimientos');
};

const handleProductAdded = (productData: any) => {
  console.log('Producto agregado exitosamente:', productData);
  toast.add({
    severity: 'success',
    summary: 'Producto Agregado',
    detail: 'El producto se agregó correctamente al movimiento',
    life: 3000
  });
  emit('reload-details');
};
</script>
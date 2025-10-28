<template>
  <Dialog 
    v-model:visible="visible" 
    :style="{ width: '500px' }" 
    header="Confirmar Eliminación" 
    :modal="true"
    :closable="!loading"
  >
    <div class="flex flex-col gap-4">
      <div class="flex items-start gap-3">
        <i class="pi pi-exclamation-triangle text-4xl text-orange-500"></i>
        <div class="flex-1">
          <p class="text-lg font-semibold mb-2">¿Estás seguro de eliminar este pago?</p>
          <p class="mb-3">Esta acción no se puede deshacer.</p>
          
          <div v-if="pago" class="space-y-2">
            <div class="flex justify-between">
              <span class="">Empleado:</span>
              <span class="font-semibold">{{ pago.empleado }}</span>
            </div>
            <div class="flex justify-between">
              <span class="">Monto:</span>
              <span class="font-semibold text-green-600">{{ pago.monto_formateado }}</span>
            </div>
            <div class="flex justify-between">
              <span class="">Fecha:</span>
              <span class="font-semibold">{{ pago.fecha_pago_formateada }}</span>
            </div>
            <div class="flex justify-between">
              <span class="">Periodo:</span>
              <span class="font-semibold">{{ pago.periodo }}</span>
            </div>
            <div class="flex justify-between">
              <span class="">Estado:</span>
              <Tag 
                :value="pago.estado" 
                :severity="getEstadoSeverity(pago.estado)"
              />
            </div>
          </div>
        </div>
      </div>

      <Message v-if="pago?.tiene_comprobante" severity="warn" :closable="false">
        <p class="text-sm">
          <i class="pi pi-info-circle mr-1"></i>
          El comprobante asociado también será eliminado.
        </p>
      </Message>
    </div>

    <template #footer>
      <Button 
        label="Cancelar" 
        icon="pi pi-times" 
        @click="closeDialog"
        text
        severity="secondary"
        :disabled="loading"
      />
      <Button 
        label="Eliminar" 
        icon="pi pi-trash" 
        severity="danger"
        @click="confirmDelete"
        :loading="loading"
      />
    </template>
  </Dialog>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import Message from 'primevue/message';
import { useToast } from 'primevue/usetoast';
import { usePagosPersonal, type PagoPersonal } from './usePagosPersonal';

interface Props {
  visible: boolean;
  pago: PagoPersonal | null;
}

interface Emits {
  (e: 'update:visible', value: boolean): void;
  (e: 'deleted'): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();
const toast = useToast();
const pagosStore = usePagosPersonal();

const loading = ref(false);

const visible = computed({
  get: () => props.visible,
  set: (value) => emit('update:visible', value)
});

const getEstadoSeverity = (estado: string) => {
  const severities: Record<string, string> = {
    'pagado': 'success',
    'pendiente': 'warn',
    'anulado': 'danger'
  };
  return severities[estado] || 'info';
};

const confirmDelete = async () => {
  if (!props.pago) return;

  loading.value = true;

  try {
    await pagosStore.deletePago(props.pago.id);

    toast.add({ 
      severity: 'success', 
      summary: 'Éxito', 
      detail: 'Pago eliminado correctamente', 
      life: 3000 
    });

    emit('deleted');
    closeDialog();
  } catch (error: any) {
    toast.add({ 
      severity: 'error', 
      summary: 'Error', 
      detail: error.response?.data?.message || 'No se pudo eliminar el pago', 
      life: 3000 
    });
  } finally {
    loading.value = false;
  }
};

const closeDialog = () => {
  visible.value = false;
};
</script>
<template>
  <Dialog :visible="visible" @update:visible="updateVisible" modal :style="{ width: '450px' }" header="Cambiar Estado de Habitación">
    <div v-if="room" class="space-y-4">
      <div class="p-3 rounded-lg">
        <div class="flex items-center justify-between mb-2">
          <span class="font-semibold">Habitación:</span>
          <Tag severity="info" class="font-mono">{{ room.room_number }}</Tag>
        </div>
        <div class="flex items-center justify-between">
          <span class="font-semibold">Estado actual:</span>
          <Tag :severity="getStatusSeverity(room.status)">
            {{ getStatusLabel(room.status) }}
          </Tag>
        </div>
      </div>

      <form @submit.prevent="submitForm" class="space-y-4">
        <div>
          <label for="new_status" class="block text-sm font-medium mb-2">Nuevo Estado *</label>
          <Select id="new_status" v-model="form.new_status" :options="statusOptions" 
            optionLabel="label" optionValue="value" placeholder="Seleccionar nuevo estado" 
            class="w-full" :class="{ 'p-invalid': errors.new_status }">
            <template #value="slotProps">
              <div v-if="slotProps.value" class="flex items-center gap-2">
                <i :class="getStatusIcon(slotProps.value)"></i>
                <span>{{ getStatusLabel(slotProps.value) }}</span>
              </div>
              <span v-else>{{ slotProps.placeholder }}</span>
            </template>
            <template #option="slotProps">
              <div class="flex items-center gap-2">
                <i :class="getStatusIcon(slotProps.option.value)"></i>
                <span>{{ slotProps.option.label }}</span>
              </div>
            </template>
          </Select>
          <small v-if="errors.new_status" class="p-error">{{ errors.new_status[0] }}</small>
        </div>

        <div>
          <label for="reason" class="block text-sm font-medium mb-2">Motivo (Opcional)</label>
          <Textarea id="reason" v-model="form.reason" rows="3" class="w-full"
            placeholder="Describe el motivo del cambio de estado..." />
        </div>
      </form>
    </div>

    <template #footer>
      <Button label="Cancelar" icon="pi pi-times" text severity="secondary" 
        @click="closeDialog" :disabled="loading" />
      <Button label="Cambiar Estado" icon="pi pi-refresh" severity="contrast" 
        @click="submitForm" :loading="loading" :disabled="!form.new_status" />
    </template>
  </Dialog>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';
import axios from 'axios';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import Select from 'primevue/select';
import Textarea from 'primevue/textarea';
import Tag from 'primevue/tag';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
  visible: { type: Boolean, required: true },
  room: { type: Object, default: null }
});

const emit = defineEmits(['update:visible', 'actualizado']);

const toast = useToast();
const loading = ref(false);

const statusOptions = [
  { label: 'Disponible', value: 'available' },
  { label: 'Ocupada', value: 'occupied' },
  { label: 'Mantenimiento', value: 'maintenance' },
  { label: 'Limpieza', value: 'cleaning' }
];

const form = reactive({
  new_status: null,  // Cambiado de 'status' a 'new_status'
  reason: ''
});

const errors = ref({});

// Función para actualizar la visibilidad
function updateVisible(value) {
  emit('update:visible', value);
}

watch(() => props.visible, (newVal) => {
  if (newVal && props.room) {
    form.new_status = null;  // Cambiado de 'status' a 'new_status'
    form.reason = '';
    errors.value = {};
  }
});

function getStatusSeverity(status) {
  const severities = {
    'available': 'success',
    'occupied': 'danger',
    'maintenance': 'warn',
    'cleaning': 'info'
  };
  return severities[status] || 'secondary';
}

function getStatusLabel(status) {
  const labels = {
    'available': 'Disponible',
    'occupied': 'Ocupada',
    'maintenance': 'Mantenimiento',
    'cleaning': 'Limpieza'
  };
  return labels[status] || status;
}

function getStatusIcon(status) {
  const icons = {
    'available': 'pi pi-check-circle text-green-500',
    'occupied': 'pi pi-lock text-red-500',
    'maintenance': 'pi pi-wrench text-orange-500',
    'cleaning': 'pi pi-sparkles text-blue-500'
  };
  return icons[status] || 'pi pi-circle';
}

function closeDialog() {
  emit('update:visible', false);
  form.new_status = null;  // Cambiado de 'status' a 'new_status'
  form.reason = '';
  errors.value = {};
}

async function submitForm() {
  if (!props.room || !form.new_status) return;  // Cambiado de 'status' a 'new_status'
  
  try {
    loading.value = true;
    errors.value = {};

    const payload = {
      new_status: form.new_status,  // Cambiado de 'status' a 'new_status'
      reason: form.reason || null
    };

    await axios.patch(`/rooms/${props.room.id}/status`, payload);

    toast.add({
      severity: 'success',
      summary: 'Éxito',
      detail: 'Estado de la habitación actualizado correctamente',
      life: 3000
    });

    emit('actualizado');
    closeDialog();

  } catch (error) {
    console.error('Error cambiando estado:', error);

    if (error.response?.status === 422) {
      errors.value = error.response.data.errors;
    } else {
      toast.add({
        severity: 'error',
        summary: 'Error',
        detail: 'No se pudo cambiar el estado de la habitación',
        life: 3000
      });
    }
  } finally {
    loading.value = false;
  }
}
</script>
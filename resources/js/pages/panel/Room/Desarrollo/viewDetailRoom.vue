<template>
  <Dialog :visible="visible" @update:visible="updateVisible" modal :style="{ width: '700px' }" header="Detalle de la Habitación">
    <div v-if="loadingDetail" class="flex justify-center items-center py-8">
      <i class="pi pi-spin pi-spinner text-4xl text-gray-400"></i>
    </div>

    <div v-else-if="roomDetail" class="space-y-4">
      <!-- Header con número y estado -->
      <div class="flex items-center justify-between pb-4 border-b">
        <div>
          <h3 class="text-2xl font-bold flex items-center gap-2">
            <i class="pi pi-building text-gray-600"></i>
            Habitación {{ roomDetail.room_number }}
          </h3>
          <p v-if="roomDetail.name" class="text-gray-600 mt-1">{{ roomDetail.name }}</p>
        </div>
        <Tag :severity="getStatusSeverity(roomDetail.status)" class="text-lg px-4 py-2">
          {{ getStatusLabel(roomDetail.status) }}
        </Tag>
      </div>

      <!-- Información general -->
      <div class="grid grid-cols-2 gap-4">
        <div class="space-y-3">
          <div>
            <label class="text-sm font-semibold block mb-1">Estado</label>
            <div class="flex items-center gap-2">
              <Tag :severity="roomDetail.is_active ? 'success' : 'danger'">
                {{ roomDetail.is_active ? 'Activo' : 'Inactivo' }}
              </Tag>
            </div>
          </div>

          <div>
            <label class="text-sm font-semibold block mb-1">Tipo de Habitación</label>
            <p class="text-base font-medium">{{ roomDetail.room_type?.name || 'N/A' }}</p>
          </div>

          <div>
            <label class="text-sm font-semibold block mb-1">Capacidad</label>
            <p class="text-base">
              <i class="pi pi-users"></i>
              {{ roomDetail.room_type?.capacity || 0 }} personas
            </p>
          </div>
        </div>

        <div class="space-y-3">
          <div>
            <label class="text-sm font-semibold block mb-1">Ubicación</label>
            <p class="text-base">{{ roomDetail.floor?.name || 'N/A' }}</p>
            <small class="">Piso {{ roomDetail.floor?.floor_number }}</small>
          </div>

          <div>
            <label class="text-sm font-semibold block mb-1">Nombre Completo</label>
            <p class="text-sm">{{ roomDetail.full_name }}</p>
          </div>
        </div>
      </div>

      <!-- Precios -->
      <div v-if="roomDetail.room_type" class="p-4 rounded-lg">
        <h4 class="font-semibold mb-3 flex items-center gap-2">
          <i class="pi pi-money-bill"></i>
          Tarifas
        </h4>
        <div class="grid grid-cols-3 gap-3">
          <div class="text-center">
            <p class="text-sm">Por Hora</p>
            <p class="text-lg font-bold text-green-600">
              S/ {{ roomDetail.room_type.base_price_per_hour }}
            </p>
          </div>
          <div class="text-center">
            <p class="text-sm">Por Noche</p>
            <p class="text-lg font-bold text-blue-600">
              S/ {{ roomDetail.room_type.base_price_per_night }}
            </p>
          </div>
          <div class="text-center">
            <p class="text-sm">Por Día</p>
            <p class="text-lg font-bold text-purple-600">
              S/ {{ roomDetail.room_type.base_price_per_day }}
            </p>
          </div>
        </div>
      </div>

      <!-- Descripción -->
      <div v-if="roomDetail.description || roomDetail.room_type?.description">
        <label class="text-sm font-semibold block mb-2">Descripción</label>
        <div class="p-3 rounded-lg">
          <p class="text-sm">
            {{ roomDetail.description || roomDetail.room_type?.description }}
          </p>
        </div>
      </div>

      <!-- Reserva actual -->
      <div v-if="roomDetail.current_booking" class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
        <h4 class="font-semibold mb-2 flex items-center gap-2 text-blue-700">
          <i class="pi pi-calendar"></i>
          Reserva Actual
        </h4>
        <p class="text-sm text-gray-700">
          Esta habitación tiene una reserva activa
        </p>
      </div>

      <!-- Fechas -->
      <div class="grid grid-cols-2 gap-4 pt-3 border-t text-xs">
        <div>
          <span class="font-semibold">Creado:</span>
          {{ formatDate(roomDetail.created_at) }}
        </div>
        <div>
          <span class="font-semibold">Actualizado:</span>
          {{ formatDate(roomDetail.updated_at) }}
        </div>
      </div>
    </div>

    <template #footer>
      <Button label="Cerrar" icon="pi pi-times" severity="secondary" text @click="closeDialog" />
    </template>
  </Dialog>
</template>

<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import Tag from 'primevue/tag';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
  visible: { type: Boolean, required: true },
  roomId: { type: String, default: null }
});

const emit = defineEmits(['update:visible']);

const toast = useToast();
const loadingDetail = ref(false);
const roomDetail = ref(null);

// Función para actualizar la visibilidad
function updateVisible(value) {
  emit('update:visible', value);
}

watch(() => props.visible, async (newVal) => {
  if (newVal && props.roomId) {
    await fetchRoomDetail();
  }
});

async function fetchRoomDetail() {
  try {
    loadingDetail.value = true;
    const response = await axios.get(`/rooms/${props.roomId}`);
    roomDetail.value = response.data.data || response.data;
  } catch (error) {
    console.error('Error cargando detalle:', error);
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'No se pudo cargar el detalle de la habitación',
      life: 3000
    });
    closeDialog();
  } finally {
    loadingDetail.value = false;
  }
}

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

function formatDate(dateString) {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return date.toLocaleString('es-PE', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  });
}

function closeDialog() {
  emit('update:visible', false);
  roomDetail.value = null;
}
</script>
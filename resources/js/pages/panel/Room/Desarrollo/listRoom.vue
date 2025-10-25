<template>
  <div>
    <DataTable ref="dt" v-model:selection="selectedRooms" :value="localRooms" dataKey="id" :paginator="true" :rows="10"
      :filters="filters"
      paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
      :rowsPerPageOptions="[5, 10, 25]"
      currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} habitaciones" class="p-datatable-sm">
      <template #header>
        <div class="flex flex-wrap gap-2 items-center justify-between">
          <h4 class="m-0">
            HABITACIONES
            <Tag severity="contrast">{{ total }}</Tag>
          </h4>
          <div class="flex gap-2">
            <Select v-model="statusFilter" :options="statusFilterOptions" optionLabel="label" optionValue="value"
              placeholder="Filtrar por estado" class="w-48" @change="applyStatusFilter" />
            <IconField>
              <InputIcon>
                <i class="pi pi-search" />
              </InputIcon>
              <InputText v-model="filters['global'].value" placeholder="Buscar..." />
            </IconField>
          </div>
        </div>
      </template>

      <Column selectionMode="multiple" style="width: 3rem" :exportable="false" />

      <Column field="room_number" header="N° Habitación" sortable style="min-width: 8rem">
        <template #body="slotProps">
          <Tag severity="info" class="font-mono">{{ slotProps.data.room_number }}</Tag>
        </template>
      </Column>

      <Column field="name" header="Nombre" style="min-width: 12rem">
        <template #body="slotProps">
          <span v-if="slotProps.data.name">{{ slotProps.data.name }}</span>
          <span v-else class="text-gray-400 italic">Sin nombre</span>
        </template>
      </Column>

      <Column field="room_type" header="Tipo" sortable style="min-width: 10rem">
        <template #body="slotProps">
          <div v-if="slotProps.data.room_type">
            <div class="font-medium">{{ slotProps.data.room_type.name }}</div>
            <small class="text-gray-500">Capacidad: {{ slotProps.data.room_type.capacity }}</small>
          </div>
        </template>
      </Column>

      <Column field="status" header="Estado" sortable style="min-width: 8rem">
        <template #body="slotProps">
          <Tag :severity="getStatusSeverity(slotProps.data.status)">
            {{ getStatusLabel(slotProps.data.status) }}
          </Tag>
        </template>
      </Column>

      <Column field="description" header="Descripción" style="min-width: 15rem">
        <template #body="slotProps">
          <span v-if="slotProps.data.description">{{ slotProps.data.description }}</span>
          <span v-else class="text-gray-400 italic">Sin descripción</span>
        </template>
      </Column>

      <Column field="is_active" header="Activo" sortable style="min-width: 5rem">
        <template #body="slotProps">
          <Tag :severity="slotProps.data.is_active ? 'success' : 'danger'">
            {{ slotProps.data.is_active ? 'Sí' : 'No' }}
          </Tag>
        </template>
      </Column>

      <Column header="Acciones" style="min-width: 10rem">
        <template #body="slotProps">
          <div class="flex gap-1">
            <Button icon="pi pi-eye" rounded size="small" severity="secondary" variant="outlined"
              v-tooltip.top="'Ver detalle'" @click="onViewDetail(slotProps.data)" />

            <Button icon="pi pi-refresh" rounded size="small" severity="info" variant="outlined"
              v-tooltip.top="'Cambiar estado'" @click="onChangeStatus(slotProps.data)" />

            <Button icon="pi pi-pencil" rounded size="small" severity="warning" variant="outlined"
              v-tooltip.top="'Editar'" @click="onEdit(slotProps.data)" />

            <Button icon="pi pi-trash" rounded size="small" severity="danger" variant="outlined"
              v-tooltip.top="'Eliminar'" @click="onDelete(slotProps.data)" />
          </div>
        </template>
      </Column>
    </DataTable>

    <!-- Diálogo para ver detalle -->
    <ViewDetailRoom 
      :visible="showDetailDialog"
      @update:visible="showDetailDialog = $event"
      :room-id="selectedRoomId"
    />

    <!-- Diálogo para cambiar estado -->
    <ChangeStatusRoom 
      :visible="showStatusDialog"
      @update:visible="showStatusDialog = $event"
      :room="selectedRoom"
      @actualizado="handleUpdate"
    />

    <!-- Diálogo para editar -->
    <EditRoom 
      :visible="showEditDialog"
      @update:visible="showEditDialog = $event"
      :room="selectedRoom"
      @actualizado="handleUpdate"
    />

    <!-- Diálogo de confirmación para eliminar -->
    <ConfirmDialog group="deleteRoom">
      <template #message="slotProps">
        <div class="flex flex-col items-center gap-3 py-3">
          <i class="pi pi-exclamation-triangle text-6xl text-red-500"></i>
          <p class="font-semibold text-lg">{{ slotProps.message.header }}</p>
          <p class="">{{ slotProps.message.message }}</p>
        </div>
      </template>
    </ConfirmDialog>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import axios from 'axios';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import Button from 'primevue/button';
import Select from 'primevue/select';
import ConfirmDialog from 'primevue/confirmdialog';
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';
import ViewDetailRoom from './viewDetailRoom.vue';
import ChangeStatusRoom from './changeStatusRoom.vue';
import EditRoom from './editRoom.vue';

const props = defineProps({
  floor: { type: Object, required: true },
  rooms: { type: Array, required: true }
});

const emit = defineEmits(['actualizado']);

const toast = useToast();
const confirm = useConfirm();
const dt = ref();
const selectedRooms = ref();
const statusFilter = ref(null);

// Diálogos
const showDetailDialog = ref(false);
const showStatusDialog = ref(false);
const showEditDialog = ref(false);
const selectedRoom = ref(null);
const selectedRoomId = ref(null);

const localRooms = computed(() => {
  if (!statusFilter.value) {
    return props.rooms;
  }
  return props.rooms.filter(r => r.status === statusFilter.value);
});

const total = computed(() => localRooms.value.length);

const statusFilterOptions = [
  { label: 'Todos los estados', value: null },
  { label: 'Disponible', value: 'available' },
  { label: 'Ocupada', value: 'occupied' },
  { label: 'Mantenimiento', value: 'maintenance' },
  { label: 'Limpieza', value: 'cleaning' }
];

const filters = ref({
  'global': { value: null, matchMode: FilterMatchMode.CONTAINS },
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

function applyStatusFilter() {
  // El filtrado se hace automáticamente a través del computed
}

// Resetear el filtro cuando cambien las rooms
watch(() => props.rooms, () => {
  statusFilter.value = null;
});

// Función para ver detalle
function onViewDetail(room) {
  selectedRoomId.value = room.id;
  showDetailDialog.value = true;
}

// Función para cambiar estado
function onChangeStatus(room) {
  selectedRoom.value = room;
  showStatusDialog.value = true;
}

// Función para editar
function onEdit(room) {
  selectedRoom.value = room;
  showEditDialog.value = true;
}

// Función para eliminar
function onDelete(room) {
  confirm.require({
    group: 'deleteRoom',
    header: '¿Eliminar Habitación?',
    message: `¿Está seguro de eliminar la habitación ${room.room_number}? Esta acción no se puede deshacer.`,
    icon: 'pi pi-exclamation-triangle',
    acceptLabel: 'Sí, eliminar',
    rejectLabel: 'Cancelar',
    acceptClass: 'p-button-danger',
    accept: async () => {
      try {
        await axios.delete(`/rooms/${room.id}`);
        
        toast.add({
          severity: 'success',
          summary: 'Éxito',
          detail: 'Habitación eliminada correctamente',
          life: 3000
        });
        
        handleUpdate();
      } catch (error) {
        console.error('Error eliminando habitación:', error);
        toast.add({
          severity: 'error',
          summary: 'Error',
          detail: error.response?.data?.message || 'No se pudo eliminar la habitación',
          life: 3000
        });
      }
    }
  });
}

// Función para manejar actualizaciones
function handleUpdate() {
  emit('actualizado');
}
</script>
<template>
  <DataTable ref="dt" v-model:selection="selectedRooms" :value="rooms" dataKey="id" :paginator="true" :rows="10"
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
</template>

<script setup>
import { ref, watch } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import Button from 'primevue/button';
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import Select from 'primevue/select';

const props = defineProps({
  floor: { type: Object, required: true },
  rooms: { type: Array, required: true },
  refresh: { type: Number, required: false }
});

const toast = useToast();
const dt = ref();
const selectedRooms = ref();
const statusFilter = ref(null);

const rooms = ref([...props.rooms]);
const total = ref(props.rooms.length);

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
  if (!statusFilter.value) {
    rooms.value = [...props.rooms];
  } else {
    rooms.value = props.rooms.filter(r => r.status === statusFilter.value);
  }
  total.value = rooms.value.length;
}

watch(() => props.refresh, () => {
  rooms.value = [...props.rooms];
  total.value = props.rooms.length;
  statusFilter.value = null;
});
</script>
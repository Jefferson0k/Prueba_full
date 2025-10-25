<template>
  <DataTable ref="dt" v-model:selection="selectedFloors" :value="floors" dataKey="id" :paginator="true" :rows="10"
    :filters="filters"
    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
    :rowsPerPageOptions="[5, 10, 25]" currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} pisos"
    class="p-datatable-sm">
    <template #header>
      <div class="flex flex-wrap gap-2 items-center justify-between">
        <h4 class="m-0">
          PISOS DE {{ subBranch.name }}
          <Tag severity="contrast">{{ total }}</Tag>
        </h4>
        <IconField>
          <InputIcon>
            <i class="pi pi-search" />
          </InputIcon>
          <InputText v-model="filters['global'].value" placeholder="Buscar..." />
        </IconField>
      </div>
    </template>

    <Column selectionMode="multiple" style="width: 3rem" :exportable="false" />

    <Column field="floor_number" header="N°" sortable style="min-width: 4rem">
      <template #body="slotProps">
        <Tag :severity="getFloorNumberSeverity(slotProps.data.floor_number)">
          {{ getFloorLabel(slotProps.data.floor_number) }}
        </Tag>
      </template>
    </Column>

    <Column field="name" header="Nombre" sortable style="min-width: 12rem" />

    <Column field="description" header="Descripción" style="min-width: 20rem">
      <template #body="slotProps">
        <span v-if="slotProps.data.description">{{ slotProps.data.description }}</span>
        <span v-else class="text-gray-400 italic">Sin descripción</span>
      </template>
    </Column>

    <Column field="rooms_count" header="Habitaciones" sortable style="min-width: 8rem">
      <template #body="slotProps">
        <div class="flex items-center gap-2">
          <Tag severity="info">
            Total: {{ slotProps.data.rooms_count || 0 }}
          </Tag>
          <Tag severity="success" v-if="slotProps.data.available_rooms_count !== undefined">
            Disp: {{ slotProps.data.available_rooms_count }}
          </Tag>
        </div>
      </template>
    </Column>

    <Column field="is_active" header="Estado" sortable style="min-width: 5rem">
      <template #body="slotProps">
        <Tag :severity="slotProps.data.is_active ? 'success' : 'danger'">
          {{ slotProps.data.is_active ? 'Activo' : 'Inactivo' }}
        </Tag>
      </template>
    </Column>

    <Column field="created_at" header="Creación" sortable style="min-width: 10rem">
      <template #body="slotProps">
        {{ formatDate(slotProps.data.created_at) }}
      </template>
    </Column>

    <Column header="Acciones" style="min-width: 4rem">
      <template #body="slotProps">
        <div class="flex gap-2">
          <!-- Botón Habitaciones -->
          <Button icon="pi pi-building" rounded severity="info" variant="outlined"
            v-tooltip.top="'Gestionar Habitaciones'" @click="onManageRooms(slotProps.data)" />

          <!-- Botón Editar -->
          <Button icon="pi pi-pencil" rounded severity="warng" variant="outlined" v-tooltip.top="'Editar'"
            @click="onEdit(slotProps.data)" />

          <!-- Botón Eliminar -->
          <Button icon="pi pi-trash" rounded severity="danger" variant="outlined" v-tooltip.top="'Eliminar'"
            @click="onDelete(slotProps.data)" />
        </div>
      </template>
    </Column>
  </DataTable>

  <!-- Dialog para editar piso -->
  <Dialog v-model:visible="showEditDialog" modal :style="{ width: '450px' }" header="Editar Piso">
    <form @submit.prevent="submitEdit" class="space-y-4" v-if="editingFloor">
      <div>
        <label for="edit_name" class="block text-sm font-medium mb-1">Nombre del Piso</label>
        <InputText id="edit_name" v-model="editForm.name" class="w-full" :class="{ 'p-invalid': editErrors.name }" />
        <small v-if="editErrors.name" class="p-error">{{ editErrors.name }}</small>
      </div>

      <div>
        <label for="edit_floor_number" class="block text-sm font-medium mb-1">Número de Piso</label>
        <InputNumber id="edit_floor_number" v-model="editForm.floor_number" class="w-full"
          :class="{ 'p-invalid': editErrors.floor_number }" :min="0" />
        <small v-if="editErrors.floor_number" class="p-error">{{ editErrors.floor_number }}</small>
      </div>

      <div>
        <label for="edit_description" class="block text-sm font-medium mb-1">Descripción</label>
        <Textarea id="edit_description" v-model="editForm.description" rows="3" class="w-full" />
      </div>

      <div class="flex items-center">
        <Checkbox id="edit_is_active" v-model="editForm.is_active" binary />
        <label for="edit_is_active" class="ml-2">Activo</label>
      </div>

      <div class="flex justify-end gap-2 pt-4">
        <Button label="Cancelar" severity="secondary" text @click="closeEditDialog" />
        <Button label="Actualizar" type="submit" severity="contrast" :loading="editLoading" />
      </div>
    </form>
  </Dialog>

  <!-- Dialog de confirmación para eliminar -->
  <ConfirmDialog />
</template>

<script setup>
import { ref, watch, onMounted, reactive } from 'vue';
import axios from 'axios';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Textarea from 'primevue/textarea';
import Checkbox from 'primevue/checkbox';
import Tag from 'primevue/tag';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import ConfirmDialog from 'primevue/confirmdialog';
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  subBranch: { type: Object, required: true },
  refresh: { type: Number, required: true }
});

const toast = useToast();
const confirm = useConfirm();
const dt = ref();
const floors = ref([]);
const selectedFloors = ref();
const total = ref(0);

// Estados para edición
const showEditDialog = ref(false);
const editingFloor = ref(null);
const editLoading = ref(false);
const editForm = reactive({
  name: '',
  floor_number: 0,
  description: '',
  is_active: true
});
const editErrors = ref({});

const filters = ref({
  'global': { value: null, matchMode: FilterMatchMode.CONTAINS },
});

async function fetchFloors() {
  try {
    const res = await axios.get(`/sub-branches/${props.subBranch.id}/floors?with_room_counts=true`);
    floors.value = res.data.data || res.data;
    total.value = floors.value.length;
  } catch (error) {
    console.error('Error cargando pisos:', error);
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'No se pudieron cargar los pisos',
      life: 3000
    });
  }
}

function getFloorLabel(floorNumber) {
  if (floorNumber === 0) return 'PB';
  if (floorNumber === 1) return '1°';
  if (floorNumber === 2) return '2°';
  if (floorNumber === 3) return '3°';
  return `${floorNumber}°`;
}

function getFloorNumberSeverity(floorNumber) {
  if (floorNumber === 0) return 'secondary';
  if (floorNumber <= 2) return 'info';
  if (floorNumber <= 5) return 'success';
  return 'warning';
}

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
}

function onManageRooms(floor) {
  router.visit(`/panel/floors/${floor.id}/rooms`);
}


function onEdit(floor) {
  editingFloor.value = floor;
  editForm.name = floor.name;
  editForm.floor_number = floor.floor_number;
  editForm.description = floor.description || '';
  editForm.is_active = floor.is_active;
  editErrors.value = {};
  showEditDialog.value = true;
}

function closeEditDialog() {
  showEditDialog.value = false;
  editingFloor.value = null;
  editErrors.value = {};
}

async function submitEdit() {
  if (!editingFloor.value) return;

  try {
    editLoading.value = true;
    editErrors.value = {};

    await axios.put(`/floors/${editingFloor.value.id}`, editForm);

    toast.add({
      severity: 'success',
      summary: 'Éxito',
      detail: 'Piso actualizado correctamente',
      life: 3000
    });

    closeEditDialog();
    await fetchFloors();

  } catch (error) {
    console.error('Error actualizando piso:', error);

    if (error.response?.status === 422) {
      editErrors.value = error.response.data.errors;
    } else {
      toast.add({
        severity: 'error',
        summary: 'Error',
        detail: 'No se pudo actualizar el piso',
        life: 3000
      });
    }
  } finally {
    editLoading.value = false;
  }
}

function onDelete(floor) {
  confirm.require({
    message: `¿Está seguro de eliminar el piso "${floor.name}"?`,
    header: 'Confirmar Eliminación',
    icon: 'pi pi-exclamation-triangle',
    rejectClass: 'p-button-secondary p-button-outlined',
    rejectLabel: 'Cancelar',
    acceptLabel: 'Eliminar',
    accept: async () => {
      try {
        await axios.delete(`/floors/${floor.id}`);

        toast.add({
          severity: 'success',
          summary: 'Éxito',
          detail: 'Piso eliminado correctamente',
          life: 3000
        });

        await fetchFloors();
      } catch (error) {
        console.error('Error eliminando piso:', error);
        toast.add({
          severity: 'error',
          summary: 'Error',
          detail: error.response?.data?.message || 'No se pudo eliminar el piso',
          life: 3000
        });
      }
    }
  });
}

onMounted(fetchFloors);
watch(() => props.refresh, fetchFloors);
</script>
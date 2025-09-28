<template>
  <DataTable 
    ref="dt" 
    v-model:selection="selectedProducts" 
    :value="products" 
    dataKey="id" 
    :paginator="true" 
    :rows="10"
    :filters="filters"
    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
    :rowsPerPageOptions="[5, 10, 25]"
    currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} sub sucursales"
    class="p-datatable-sm"
  >
    <template #header>
      <div class="flex flex-wrap gap-2 items-center justify-between">
        <h4 class="m-0">
          SUB SUCURSALES
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
    <Column field="code" header="Código" sortable style="min-width: 5rem" />
    <Column field="name" header="Nombre" sortable style="min-width: 12rem" />
    <Column field="address" header="Dirección" sortable style="min-width: 25rem" />
    <Column field="phone" header="Teléfono" sortable style="min-width: 7rem" />
    
    <!-- Columna actualizada para mostrar conteos reales -->
    <Column field="pisos" header="Pisos" sortable style="min-width: 5rem">
      <template #body="slotProps">
        <Tag severity="info">{{ slotProps.data.floors_count || 0 }}</Tag>
      </template>
    </Column>
    
    <Column field="habitaciones" header="Habitaciones" sortable style="min-width: 5rem">
      <template #body="slotProps">
        <div class="flex gap-1">
          <Tag severity="success">{{ slotProps.data.rooms_count || 0 }}</Tag>
          <Tag severity="contrast" v-if="slotProps.data.available_rooms_count !== undefined">
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
    
    <Column field="creacion" header="Creación" sortable style="min-width: 12rem"></Column>
    
    <Column header="Acciones" style="min-width: 12rem">
      <template #body="slotProps">
        <div class="flex gap-2">
          <!-- Botón Habitaciones -->
          <Button 
            icon="pi pi-building" 
            rounded 
            severity="info" 
            variant="outlined" 
            v-tooltip.top="'Habitaciones'"
            @click="onManageRooms(slotProps.data)" 
          />
          
          <!-- Botón Pisos -->
          <Button 
            icon="pi pi-building" 
            rounded 
            severity="contrast" 
            variant="outlined" 
            v-tooltip.top="'Pisos'"
            @click="onManageFloors(slotProps.data)" 
          />
          
          <!-- Botón Ver Detalle -->
          <Button 
            icon="pi pi-eye" 
            rounded 
            severity="secondary" 
            variant="outlined" 
            v-tooltip.top="'Ver detalle'"
            @click="onViewDetail(slotProps.data)" 
          />
          
          <!-- Botón Editar -->
          <Button 
            icon="pi pi-pencil" 
            rounded 
            severity="warning" 
            variant="outlined" 
            v-tooltip.top="'Editar'"
            @click="onEdit(slotProps.data)" 
          />
          
          <!-- Botón Eliminar -->
          <Button 
            icon="pi pi-trash" 
            rounded 
            severity="danger" 
            variant="outlined" 
            v-tooltip.top="'Eliminar'"
            @click="onDelete(slotProps.data)" 
          />
        </div>
      </template>
    </Column>
  </DataTable>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import Button from 'primevue/button';
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  branch: { type: Object, required: true },
  refresh: { type: Number, required: true }
});

const toast = useToast();
const dt = ref();
const products = ref([]);
const selectedProducts = ref();
const total = ref(0);

const filters = ref({
  'global': { value: null, matchMode: FilterMatchMode.CONTAINS },
});

async function fetchSubBranches() {
  try {
    const res = await axios.get(`/sub-branches/${props.branch.id}?with_counts=true`);
    products.value = res.data.data;
    total.value = res.data.total;
  } catch (error) {
    console.error('Error cargando sub sucursales:', error);
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'No se pudieron cargar las sub sucursales',
      life: 3000
    });
  }
}


function onManageRooms(subBranch) {
  console.log('Gestionar habitaciones de:', subBranch);
  router.visit(`/panel/sub-branches/${subBranch.id}/rooms`);
}

function onManageFloors(subBranch) {
  router.visit(`/panel/sub-branches/${subBranch.id}/floors`);
}

function onViewDetail(subBranch) {
  console.log('Ver detalle:', subBranch);
}

function onEdit(subBranch) {
  console.log('Editar:', subBranch);
}

function onDelete(subBranch) {
  console.log('Eliminar:', subBranch);
}

onMounted(fetchSubBranches);
watch(() => props.refresh, fetchSubBranches);
</script>
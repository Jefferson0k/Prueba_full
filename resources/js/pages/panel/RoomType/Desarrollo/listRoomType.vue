<template>
  <DataTable 
    ref="dt" 
    v-model:selection="selectedProducts" 
    :value="products" 
    dataKey="id" 
    :paginator="true" 
    :rows="10"
    :filters="filters"
    :loading="loading"
    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
    :rowsPerPageOptions="[5, 10, 25]"
    currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} tipos de habitación"
    class="p-datatable-sm"
  >
    <template #header>
      <div class="flex flex-wrap gap-2 items-center justify-between">
        <h4 class="m-0">Tipos de Habitación</h4>
        <IconField>
          <InputIcon>
            <i class="pi pi-search" />
          </InputIcon>
          <InputText v-model="filters['global'].value" placeholder="Buscar..." />
        </IconField>
      </div>
    </template>

    <template #empty>
      <div class="text-center p-4">No se encontraron tipos de habitación</div>
    </template>

    <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>
        
    <Column field="name" header="Nombre" sortable style="min-width: 16rem"></Column>
    
    <Column field="capacity" header="Capacidad" sortable style="min-width: 10rem">
      <template #body="slotProps">
        <Tag :value="`${slotProps.data.capacity} personas`" severity="info" />
      </template>
    </Column>
    
    <Column field="base_price_per_hour" header="Precio/Hora" sortable style="min-width: 12rem">
      <template #body="slotProps">
        {{ formatCurrency(slotProps.data.base_price_per_hour) }}
      </template>
    </Column>
    
    <Column field="base_price_per_day" header="Precio/Día" sortable style="min-width: 12rem">
      <template #body="slotProps">
        {{ formatCurrency(slotProps.data.base_price_per_day) }}
      </template>
    </Column>
    
    <Column field="base_price_per_night" header="Precio/Noche" sortable style="min-width: 12rem">
      <template #body="slotProps">
        {{ formatCurrency(slotProps.data.base_price_per_night) }}
      </template>
    </Column>

    <Column field="is_active" header="Estado" sortable style="min-width: 10rem">
      <template #body="slotProps">
        <Tag 
          :value="slotProps.data.is_active ? 'Activo' : 'Inactivo'" 
          :severity="slotProps.data.is_active ? 'success' : 'danger'" 
        />
      </template>
    </Column>

    <Column :exportable="false" style="min-width: 12rem">
      <template #body="slotProps">
        <Button 
          icon="pi pi-pencil" 
          outlined 
          rounded 
          class="mr-2" 
          @click="editProduct(slotProps.data)" 
        />
        <Button 
          icon="pi pi-trash" 
          outlined 
          rounded 
          severity="danger" 
          @click="confirmDeleteProduct(slotProps.data)" 
        />
      </template>
    </Column>
  </DataTable>

  <ConfirmDialog></ConfirmDialog>
  <Toast />
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { FilterMatchMode } from '@primevue/core/api';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';

const confirm = useConfirm();
const toast = useToast();
const emit = defineEmits(['edit']);

const dt = ref();
const products = ref([]);
const selectedProducts = ref([]);
const loading = ref(false);
const filters = ref({
  'global': { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const fetchRoomTypes = async () => {
  try {
    loading.value = true;
    const response = await axios.get('/room-types', {
      params: {
        per_page: 100
      }
    });
    
    products.value = response.data.data || response.data;
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Error al cargar tipos de habitación',
      life: 3000
    });
  } finally {
    loading.value = false;
  }
};

const formatCurrency = (value) => {
  return new Intl.NumberFormat('es-PE', {
    style: 'currency',
    currency: 'PEN'
  }).format(value);
};

const editProduct = (product) => {
  emit('edit', product.id);
};

const confirmDeleteProduct = (product) => {
  confirm.require({
    message: `¿Está seguro de eliminar el tipo de habitación "${product.name}"?`,
    header: 'Confirmar eliminación',
    icon: 'pi pi-exclamation-triangle',
    acceptLabel: 'Sí, eliminar',
    rejectLabel: 'Cancelar',
    acceptClass: 'p-button-danger',
    accept: () => {
      deleteProduct(product.id);
    }
  });
};

const deleteProduct = async (id) => {
  try {
    loading.value = true;
    const response = await axios.delete(`/room-types/${id}`);
    
    toast.add({
      severity: 'success',
      summary: 'Éxito',
      detail: response.data.message,
      life: 3000
    });
    
    await fetchRoomTypes();
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: error.response?.data?.message || 'Error al eliminar el tipo de habitación',
      life: 3000
    });
  } finally {
    loading.value = false;
  }
};

defineExpose({
  fetchRoomTypes
});

onMounted(() => {
  fetchRoomTypes();
});
</script>
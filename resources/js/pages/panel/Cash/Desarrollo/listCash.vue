<template>
  <div>
    <!-- Filtros -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
      <div>
        <label class="block text-sm font-medium mb-2">Buscar</label>
        <InputText
          v-model="filters.search"
          placeholder="Buscar por nombre..."
          class="w-full"
          @input="debouncedSearch"
        />
      </div>

      <div>
        <label class="block text-sm font-medium mb-2">Estado</label>
        <Select
          v-model="filters.status"
          :options="statusOptions"
          optionLabel="label"
          optionValue="value"
          placeholder="Todos"
          class="w-full"
          @change="loadCashRegisters"
        />
      </div>

      <div>
        <label class="block text-sm font-medium mb-2">Activo</label>
        <Select
          v-model="filters.is_active"
          :options="activeOptions"
          optionLabel="label"
          optionValue="value"
          placeholder="Todos"
          class="w-full"
          @change="loadCashRegisters"
        />
      </div>

      <div class="flex items-end">
        <Button
          label="Limpiar Filtros"
          icon="pi pi-filter-slash"
          @click="clearFilters"
          severity="secondary"
          outlined
          class="w-full"
        />
      </div>
    </div>

    <!-- Tabla de Cajas -->
    <DataTable
      :value="cashRegisters"
      :loading="isLoading"
      stripedRows
      paginator
      :rows="pagination.per_page"
      :totalRecords="pagination.total"
      :lazy="true"
      @page="onPage"
      @sort="onSort"
      paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
      :rowsPerPageOptions="[10, 15, 25, 50]"
      currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} cajas"
      class="p-datatable-sm"
    >
      <template #empty>
        <div class="text-center py-8">
          <i class="pi pi-inbox text-4xl text-gray-400 mb-3"></i>
          <p class="text-gray-500">No se encontraron cajas registradoras</p>
        </div>
      </template>

      <Column field="name" header="Nombre" sortable>
        <template #body="{ data }">
          <div class="flex items-center">
            <i class="pi pi-calculator mr-2 text-blue-500"></i>
            <span class="font-semibold">{{ data.name }}</span>
          </div>
        </template>
      </Column>

      <Column field="status" header="Estado" sortable>
        <template #body="{ data }">
          <Tag 
            :value="data.status.toUpperCase()" 
            :severity="getStatusSeverity(data.status)"
          />
        </template>
      </Column>

      <Column field="is_active" header="Activo" sortable>
        <template #body="{ data }">
          <Tag 
            :value="data.is_active ? 'SÍ' : 'NO'" 
            :severity="data.is_active ? 'success' : 'danger'"
          />
        </template>
      </Column>

      <Column field="opening_amount" header="Monto Apertura">
        <template #body="{ data }">
          <span v-if="data.opening_amount">
            S/. {{ formatMoney(data.opening_amount) }}
          </span>
          <span v-else class="text-gray-400">-</span>
        </template>
      </Column>

      <Column field="opened_at" header="Fecha Apertura" sortable>
        <template #body="{ data }">
          <span v-if="data.opened_at">
            {{ formatDate(data.opened_at) }}
          </span>
          <span v-else class="text-gray-400">-</span>
        </template>
      </Column>

      <Column field="opened_by_user" header="Abierto Por">
        <template #body="{ data }">
          <span v-if="data.opened_by_user">
            {{ data.opened_by_user.name }}
          </span>
          <span v-else class="text-gray-400">-</span>
        </template>
      </Column>

      <Column header="Acciones" :exportable="false">
        <template #body="{ data }">
          <div class="flex gap-2">
            <Button
              icon="pi pi-eye"
              severity="info"
              size="small"
              @click="viewCash(data.id)"
              v-tooltip.top="'Ver Detalles'"
            />
          </div>
        </template>
      </Column>
    </DataTable>

    <!-- Dialog para ver detalles -->
    <Dialog 
      v-model:visible="showDetailDialog" 
      modal 
      header="Detalles de la Caja" 
      :style="{ width: '50rem' }"
    >
      <div v-if="selectedCash" class="grid grid-cols-2 gap-4">
        <div>
          <p class="text-sm mb-1">Nombre</p>
          <p class="font-semibold">{{ selectedCash.name }}</p>
        </div>

        <div>
          <p class="text-sm mb-1">Estado</p>
          <Tag 
            :value="selectedCash.status.toUpperCase()" 
            :severity="getStatusSeverity(selectedCash.status)"
          />
        </div>

        <div>
          <p class="text-sm mb-1">Sucursal</p>
          <p class="font-semibold">{{ selectedCash.sub_branch.name }}</p>
        </div>

        <div>
          <p class="text-sm mb-1">Activo</p>
          <Tag 
            :value="selectedCash.is_active ? 'SÍ' : 'NO'" 
            :severity="selectedCash.is_active ? 'success' : 'danger'"
          />
        </div>

        <div>
          <p class="text-sm mb-1">Monto de Apertura</p>
          <p class="font-semibold">
            {{ selectedCash.opening_amount ? `S/. ${formatMoney(selectedCash.opening_amount)}` : '-' }}
          </p>
        </div>

        <div>
          <p class="text-sm mb-1">Monto de Cierre</p>
          <p class="font-semibold">
            {{ selectedCash.closing_amount ? `S/. ${formatMoney(selectedCash.closing_amount)}` : '-' }}
          </p>
        </div>

        <div>
          <p class="text-sm mb-1">Fecha de Apertura</p>
          <p class="font-semibold">
            {{ selectedCash.opened_at ? formatDate(selectedCash.opened_at) : '-' }}
          </p>
        </div>

        <div>
          <p class="text-sm mb-1">Fecha de Cierre</p>
          <p class="font-semibold">
            {{ selectedCash.closed_at ? formatDate(selectedCash.closed_at) : '-' }}
          </p>
        </div>

        <div>
          <p class="text-sm mb-1">Abierto Por</p>
          <p class="font-semibold">
            {{ selectedCash.opened_by_user ? selectedCash.opened_by_user.name : '-' }}
          </p>
        </div>

        <div>
          <p class="text-sm mb-1">Cerrado Por</p>
          <p class="font-semibold">
            {{ selectedCash.closed_by_user ? selectedCash.closed_by_user.name : '-' }}
          </p>
        </div>

        <div class="col-span-2">
          <p class="text-sm mb-1">Fecha de Creación</p>
          <p class="font-semibold">{{ formatDate(selectedCash.created_at) }}</p>
        </div>
      </div>

      <template #footer>
        <Button 
          label="Cerrar" 
          icon="pi pi-times" 
          text
          @click="showDetailDialog = false" 
          severity="secondary"
        />
      </template>
    </Dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import Dialog from 'primevue/dialog';
import { useToast } from 'primevue/usetoast';
import Select from 'primevue/select';

const toast = useToast();

interface CashRegister {
  id: string;
  name: string;
  status: string;
  is_active: boolean;
  opening_amount: number | null;
  closing_amount: number | null;
  opened_at: string | null;
  closed_at: string | null;
  sub_branch: {
    id: string;
    name: string;
  };
  opened_by_user: {
    id: string;
    name: string;
    email: string;
  } | null;
  closed_by_user: {
    id: string;
    name: string;
    email: string;
  } | null;
  created_at: string;
  updated_at: string;
  is_open: boolean;
  is_closed: boolean;
  is_blocked: boolean;
}

const cashRegisters = ref<CashRegister[]>([]);
const isLoading = ref(false);
const showDetailDialog = ref(false);
const selectedCash = ref<CashRegister | null>(null);

const filters = ref({
  search: '',
  status: null,
  is_active: null,
  sort_field: 'name',
  sort_order: 'asc'
});

const pagination = ref({
  current_page: 1,
  per_page: 15,
  total: 0,
  last_page: 1
});

const statusOptions = [
  { label: 'Todos', value: null },
  { label: 'Abierta', value: 'abierta' },
  { label: 'Cerrada', value: 'cerrada' },
  { label: 'Bloqueada', value: 'bloqueada' }
];

const activeOptions = [
  { label: 'Todos', value: null },
  { label: 'Activo', value: true },
  { label: 'Inactivo', value: false }
];

const loadCashRegisters = async () => {
  isLoading.value = true;

  try {
    const params: any = {
      sort_field: filters.value.sort_field,
      sort_order: filters.value.sort_order,
      page: pagination.value.current_page,
      per_page: pagination.value.per_page
    };

    if (filters.value.search) params.search = filters.value.search;
    if (filters.value.status) params.status = filters.value.status;
    if (filters.value.is_active !== null) params.is_active = filters.value.is_active;

    const response = await axios.get(route('cash.cash-registers.index'), { params });

    if (response.data.success) {
      cashRegisters.value = response.data.data;
      pagination.value = {
        current_page: response.data.meta.current_page,
        per_page: response.data.meta.per_page,
        total: response.data.meta.total,
        last_page: response.data.meta.last_page
      };
    }
  } catch (error) {
    console.error('Error:', error);
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Error al cargar las cajas registradoras',
      life: 3000
    });
  } finally {
    isLoading.value = false;
  }
};

let searchTimeout: NodeJS.Timeout;
const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    loadCashRegisters();
  }, 500);
};

const onPage = (event: any) => {
  pagination.value.current_page = event.page + 1;
  pagination.value.per_page = event.rows;
  loadCashRegisters();
};

const onSort = (event: any) => {
  filters.value.sort_field = event.sortField;
  filters.value.sort_order = event.sortOrder === 1 ? 'asc' : 'desc';
  loadCashRegisters();
};

const clearFilters = () => {
  filters.value = {
    search: '',
    status: null,
    is_active: null,
    sort_field: 'name',
    sort_order: 'asc'
  };
  loadCashRegisters();
};

const viewCash = async (id: string) => {
  try {
    const response = await axios.get(route('cash.cash-registers.show', id));

    if (response.data.success) {
      selectedCash.value = response.data.data;
      showDetailDialog.value = true;
    }
  } catch (error) {
    console.error('Error:', error);
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Error al cargar los detalles de la caja',
      life: 3000
    });
  }
};

const getStatusSeverity = (status: string) => {
  switch (status) {
    case 'abierta':
      return 'success';
    case 'cerrada':
      return 'secondary';
    case 'bloqueada':
      return 'danger';
    default:
      return 'info';
  }
};

const formatMoney = (value: number) => {
  return new Intl.NumberFormat('es-PE', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value);
};

const formatDate = (date: string) => {
  return new Date(date).toLocaleString('es-PE', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  });
};

onMounted(() => {
  loadCashRegisters();
});

defineExpose({
  loadCashRegisters
});
</script>
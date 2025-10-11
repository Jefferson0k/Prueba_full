<template>
  <DataTable ref="dt" v-model:selection="selectedRecords" :value="kardexGeneralStore.kardexData" 
    dataKey="id" :paginator="true" :rows="15" :filters="filters"
    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
    :rowsPerPageOptions="[10, 15, 25, 50]"
    :currentPageReportTemplate="`Mostrando {first} a {last} de ${kardexGeneralStore.pagination.total} registros`"
    class="p-datatable-sm"
    :loading="kardexGeneralStore.loading"
    stripedRows
    showGridlines>
    
    <template #header>
      <div class="flex flex-wrap gap-2 items-center justify-between">
        <div>
          <h4 class="m-0">Kardex General</h4>
          <p class="text-sm text-gray-500 mt-1">
            Total de registros: {{ kardexGeneralStore.pagination.total }}
          </p>
        </div>
        <div class="flex gap-2">
          <IconField>
            <InputIcon>
              <i class="pi pi-search" />
            </InputIcon>
            <InputText v-model="filters['global'].value" placeholder="Buscar en resultados..." />
          </IconField>
          <Button icon="pi pi-download" label="Exportar" severity="success" @click="exportData" />
        </div>
      </div>
    </template>

    <template #empty>
      <div class="text-center py-8">
        <i class="pi pi-inbox text-4xl text-gray-400 mb-3"></i>
        <p class="text-gray-600">No hay registros de kardex disponibles.</p>
        <p class="text-sm text-gray-500">Intente ajustar los filtros de búsqueda.</p>
      </div>
    </template>

    <Column selectionMode="multiple" style="width: 3rem" :exportable="false" frozen></Column>
    
    <Column field="created_at" header="Fecha" sortable style="min-width: 11rem" frozen>
      <template #body="{ data }">
        <div class="flex flex-col">
          <span class="font-semibold">{{ data.created_at }}</span>
        </div>
      </template>
    </Column>
    
    <Column field="product_nombre" header="Producto" sortable style="min-width: 16rem">
      <template #body="{ data }">
        <div class="flex flex-col">
          <span class="font-semibold">{{ data.product_nombre }}</span>
          <span class="text-xs text-gray-500">ID: {{ data.product_id.substring(0, 8) }}...</span>
        </div>
      </template>
    </Column>

    <Column field="sub_branch.name" header="Sucursal" sortable style="min-width: 14rem">
      <template #body="{ data }">
        <div v-if="data.sub_branch" class="flex flex-col">
          <span class="font-semibold">{{ data.sub_branch.name }}</span>
          <span class="text-xs text-gray-500">{{ data.sub_branch.code }}</span>
        </div>
        <span v-else class="text-gray-400">-</span>
      </template>
    </Column>
    
    <Column field="movement_category" header="Categoría" sortable style="min-width: 11rem">
      <template #body="{ data }">
        <Tag 
          :value="data.movement_category.toUpperCase()" 
          :severity="getMovementCategorySeverity(data.movement_category)" 
        />
      </template>
    </Column>
    
    <Column field="movement_type" header="Tipo" sortable style="min-width: 10rem">
      <template #body="{ data }">
        <Tag 
          :value="data.movement_type.toUpperCase()" 
          :severity="data.movement_type === 'entrada' ? 'success' : 'danger'" 
          :icon="data.movement_type === 'entrada' ? 'pi pi-arrow-down' : 'pi pi-arrow-up'"
        />
      </template>
    </Column>
    
    <Column field="movement_detail_id" header="Código Movimiento" sortable style="min-width: 14rem">
      <template #body="{ data }">
        <span class="text-xs font-mono bg-gray-100 px-2 py-1 rounded">
          {{ data.movement_detail_id.substring(0, 16) }}...
        </span>
      </template>
    </Column>
    
    <Column field="precio_total" header="Precio Total" sortable style="min-width: 11rem">
      <template #body="{ data }">
        <span class="font-semibold text-blue-600">
          S/ {{ parseFloat(data.precio_total).toFixed(2) }}
        </span>
      </template>
    </Column>
    
    <Column header="Saldo Anterior" style="min-width: 12rem">
      <template #body="{ data }">
        <div class="flex flex-col text-sm">
          <span>Caja: <strong>{{ parseFloat(data.SAnteriorCaja).toFixed(2) }}</strong></span>
          <span>Frac: <strong>{{ parseFloat(data.SAnteriorFraccion).toFixed(2) }}</strong></span>
        </div>
      </template>
    </Column>
    
    <Column header="Cantidad Movimiento" style="min-width: 14rem">
      <template #body="{ data }">
        <div class="flex flex-col text-sm">
          <span :class="getCantidadClass(data.cantidadCaja)">
            Caja: <strong>{{ parseFloat(data.cantidadCaja).toFixed(2) }}</strong>
          </span>
          <span :class="getCantidadClass(data.cantidadFraccion)">
            Frac: <strong>{{ parseFloat(data.cantidadFraccion).toFixed(2) }}</strong>
          </span>
        </div>
      </template>
    </Column>
    
    <Column header="Saldo Parcial" style="min-width: 12rem">
      <template #body="{ data }">
        <div class="flex flex-col text-sm font-bold">
          <span class="text-green-700">Caja: {{ parseFloat(data.SParcialCaja).toFixed(2) }}</span>
          <span class="text-green-700">Frac: {{ parseFloat(data.SParcialFraccion).toFixed(2) }}</span>
        </div>
      </template>
    </Column>

    <Column field="sale_id" header="Venta ID" sortable style="min-width: 14rem">
      <template #body="{ data }">
        <span v-if="data.sale_id" class="text-xs font-mono">{{ data.sale_id.substring(0, 16) }}...</span>
        <span v-else class="text-gray-400">-</span>
      </template>
    </Column>

    <Column field="estado" header="Estado" sortable style="min-width: 8rem">
      <template #body="{ data }">
        <Tag 
          :value="data.estado === 1 ? 'ACTIVO' : 'INACTIVO'" 
          :severity="data.estado === 1 ? 'success' : 'danger'" 
        />
      </template>
    </Column>

  </DataTable>
</template>

<script setup>
import { ref } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import { useKardexGeneralStore } from './kardexGeneralStore';

const kardexGeneralStore = useKardexGeneralStore();

const dt = ref();
const selectedRecords = ref();
const filters = ref({
  'global': {value: null, matchMode: FilterMatchMode.CONTAINS},
});

// Función para obtener el severity según la categoría de movimiento
const getMovementCategorySeverity = (category) => {
  const severityMap = {
    'compra': 'info',
    'venta': 'success',
    'ajuste': 'warn',
    'devolucion': 'danger',
    'traslado': 'secondary'
  };
  return severityMap[category] || 'secondary';
};

// Función para obtener las clases de color según la cantidad
const getCantidadClass = (cantidad) => {
  const value = parseFloat(cantidad);
  if (value > 0) return 'text-green-600';
  if (value < 0) return 'text-red-600';
  return 'text-gray-600';
};

// Función para exportar datos
const exportData = () => {
  dt.value.exportCSV();
};
</script>
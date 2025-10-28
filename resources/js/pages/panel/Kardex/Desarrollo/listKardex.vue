<template>
  <DataTable ref="dt" v-model:selection="selectedProducts" :value="kardexStore.kardexData" dataKey="id" 
    :paginator="true" :rows="10" :filters="filters"
    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
    :rowsPerPageOptions="[5, 10, 25]"
    currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} registros" 
    class="p-datatable-sm"
    :loading="kardexStore.loading">
    
    <template #header>
      <div class="flex flex-wrap gap-2 items-center justify-between">
        <h4 class="m-0">Kardex {{ kardexStore.productoNombre ? `- ${kardexStore.productoNombre}` : '' }}</h4>
        <IconField>
          <InputIcon>
            <i class="pi pi-search" />
          </InputIcon>
          <InputText v-model="filters['global'].value" placeholder="Buscar..." />
        </IconField>
      </div>
    </template>

    <template #empty>
      <div class="text-center py-4">
        <p v-if="!kardexStore.kardexData || kardexStore.kardexData.length === 0">
          No hay registros de kardex. Seleccione un producto, fechas y sucursal para comenzar.
        </p>
      </div>
    </template>

    <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>
    <Column field="created_at" header="Fecha" sortable style="min-width: 12rem"></Column>
    <Column field="movement_category" header="Categoría" sortable style="min-width: 12rem">
      <template #body="{ data }">
        <span :class="{'text-green-600 font-semibold': data.movement_category === 'compra', 'text-red-600 font-semibold': data.movement_category === 'venta'}">
          {{ data.movement_category.toUpperCase() }}
        </span>
      </template>
    </Column>
    <Column field="movement_type" header="Tipo Movimiento" sortable style="min-width: 12rem">
      <template #body="{ data }">
        <Tag :value="data.movement_type" :severity="data.movement_type === 'entrada' ? 'success' : 'danger'" />
      </template>
    </Column>
    <Column field="movementDetail" header="Código Mov." sortable style="min-width: 10rem">
      <template #body="{ data }">
        <span class="text-xs font-mono">{{ data.movementDetail }}</span>
      </template>
    </Column>
    <Column field="precio_total" header="Precio Total" sortable style="min-width: 10rem">
      <template #body="{ data }">
        S/ {{ parseFloat(data.precio_total).toFixed(2) }}
      </template>
    </Column>
    <Column field="SAnteriorCaja" header="S. Ant. Caja" sortable style="min-width: 10rem">
      <template #body="{ data }">
        {{ parseFloat(data.SAnteriorCaja).toFixed(2) }}
      </template>
    </Column>
    <Column field="SAnteriorFraccion" header="S. Ant. Frac." sortable style="min-width: 10rem">
      <template #body="{ data }">
        {{ parseFloat(data.SAnteriorFraccion).toFixed(2) }}
      </template>
    </Column>
    <Column field="cantidadCaja" header="Cantidad Caja" sortable style="min-width: 10rem">
      <template #body="{ data }">
        <span :class="{'text-green-600': parseFloat(data.cantidadCaja) > 0, 'text-red-600': parseFloat(data.cantidadCaja) < 0}">
          {{ parseFloat(data.cantidadCaja).toFixed(2) }}
        </span>
      </template>
    </Column>
    <Column field="cantidadFraccion" header="Cantidad Frac." sortable style="min-width: 10rem">
      <template #body="{ data }">
        <span :class="{'text-green-600': parseFloat(data.cantidadFraccion) > 0, 'text-red-600': parseFloat(data.cantidadFraccion) < 0}">
          {{ parseFloat(data.cantidadFraccion).toFixed(2) }}
        </span>
      </template>
    </Column>
    <Column field="SParcialCaja" header="S. Parcial Caja" sortable style="min-width: 12rem">
      <template #body="{ data }">
        <strong>{{ parseFloat(data.SParcialCaja).toFixed(2) }}</strong>
      </template>
    </Column>
    <Column field="SParcialFraccion" header="S. Parcial Frac." sortable style="min-width: 12rem">
      <template #body="{ data }">
        <strong>{{ parseFloat(data.SParcialFraccion).toFixed(2) }}</strong>
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
import Tag from 'primevue/tag';
import { useKardexStore } from './kardexStore';

const kardexStore = useKardexStore();

const dt = ref();
const selectedProducts = ref();
const filters = ref({
  'global': {value: null, matchMode: FilterMatchMode.CONTAINS},
});
</script>
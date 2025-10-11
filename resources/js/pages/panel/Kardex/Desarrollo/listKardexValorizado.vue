<template>
  <DataTable ref="dt" v-model:selection="selectedRecords" :value="kardexValorizadoStore.kardexData" 
    dataKey="id" :paginator="true" :rows="15" :filters="filters"
    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
    :rowsPerPageOptions="[10, 15, 25, 50]"
    :currentPageReportTemplate="`Mostrando {first} a {last} de ${kardexValorizadoStore.pagination.total} registros`"
    class="p-datatable-sm"
    :loading="kardexValorizadoStore.loading"
    stripedRows
    showGridlines
    :scrollable="true"
    scrollHeight="600px">
    
    <template #header>
      <div class="flex flex-wrap gap-2 items-center justify-between">
        <div>
          <h4 class="m-0">Kardex Valorizado</h4>
          <p class="text-sm text-gray-500 mt-1" v-if="kardexValorizadoStore.productoNombre">
            Producto: <strong>{{ kardexValorizadoStore.productoNombre }}</strong> | 
            Total de registros: <strong>{{ kardexValorizadoStore.pagination.total }}</strong>
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
        <i class="pi pi-chart-line text-4xl text-gray-400 mb-3"></i>
        <p class="text-gray-600">No hay registros de kardex valorizado.</p>
        <p class="text-sm text-gray-500">Seleccione un producto, fechas y sucursal para comenzar.</p>
      </div>
    </template>

    <!-- Columnas principales -->
    <Column selectionMode="multiple" style="width: 3rem" :exportable="false" frozen></Column>
    
    <Column field="fecha" header="Fecha" sortable style="min-width: 11rem" frozen>
      <template #body="{ data }">
        <div class="flex items-center gap-2">
          <i class="pi pi-calendar text-blue-500"></i>
          <span class="font-semibold">{{ data.fecha }}</span>
        </div>
      </template>
    </Column>
    
    <Column field="producto" header="Producto" sortable style="min-width: 18rem">
      <template #body="{ data }">
        <div class="flex flex-col">
          <span class="font-semibold text-gray-800">{{ data.producto }}</span>
        </div>
      </template>
    </Column>

    <Column field="sucursal" header="Sucursal" sortable style="min-width: 14rem">
      <template #body="{ data }">
        <div class="flex items-center gap-2">
          <i class="pi pi-building text-purple-500"></i>
          <span>{{ data.sucursal }}</span>
        </div>
      </template>
    </Column>
    
    <Column field="tipo_movimiento" header="Tipo Movimiento" sortable style="min-width: 11rem">
      <template #body="{ data }">
        <Tag 
          :value="data.tipo_movimiento.toUpperCase()" 
          :severity="data.tipo_movimiento.toLowerCase() === 'entrada' ? 'success' : 'danger'" 
          :icon="data.tipo_movimiento.toLowerCase() === 'entrada' ? 'pi pi-arrow-down' : 'pi pi-arrow-up'"
        />
      </template>
    </Column>

    <!-- Columnas de Cantidades -->
    <ColumnGroup type="header">
      <Row>
        <Column header="Cantidades Movimiento" :colspan="2" style="text-align: center; background-color: #f8f9fa;" />
      </Row>
      <Row>
        <Column header="Caja" field="cantidad_caja" sortable style="min-width: 9rem" />
        <Column header="Fracción" field="cantidad_fraccion" sortable style="min-width: 9rem" />
      </Row>
    </ColumnGroup>

    <Column field="cantidad_caja" style="min-width: 9rem">
      <template #body="{ data }">
        <span :class="getCantidadClass(data.cantidad_caja)" class="font-semibold">
          {{ parseFloat(data.cantidad_caja).toFixed(2) }}
        </span>
      </template>
    </Column>

    <Column field="cantidad_fraccion" style="min-width: 9rem">
      <template #body="{ data }">
        <span :class="getCantidadClass(data.cantidad_fraccion)" class="font-semibold">
          {{ parseFloat(data.cantidad_fraccion).toFixed(2) }}
        </span>
      </template>
    </Column>

    <!-- Columnas de Costos -->
    <Column field="costo_unitario" header="Costo Unitario" sortable style="min-width: 11rem">
      <template #body="{ data }">
        <div class="flex items-center gap-1">
          <span class="text-xs text-gray-500">S/</span>
          <span class="font-semibold text-blue-700">{{ data.costo_unitario }}</span>
        </div>
      </template>
    </Column>

    <Column field="costo_total" header="Costo Movimiento" sortable style="min-width: 12rem">
      <template #body="{ data }">
        <div class="flex items-center gap-1">
          <span class="text-xs text-gray-500">S/</span>
          <span class="font-bold text-blue-900">{{ data.costo_total }}</span>
        </div>
      </template>
    </Column>

    <!-- Columnas de Saldos -->
    <ColumnGroup type="header">
      <Row>
        <Column header="Saldo Cantidad" :colspan="2" style="text-align: center; background-color: #e8f5e9;" />
      </Row>
      <Row>
        <Column header="Caja" field="saldo_caja" sortable style="min-width: 9rem" />
        <Column header="Fracción" field="saldo_fraccion" sortable style="min-width: 9rem" />
      </Row>
    </ColumnGroup>

    <Column field="saldo_caja" style="min-width: 9rem">
      <template #body="{ data }">
        <span class="font-bold text-green-700">
          {{ parseFloat(data.saldo_caja).toFixed(2) }}
        </span>
      </template>
    </Column>

    <Column field="saldo_fraccion" style="min-width: 9rem">
      <template #body="{ data }">
        <span class="font-bold text-green-700">
          {{ parseFloat(data.saldo_fraccion).toFixed(2) }}
        </span>
      </template>
    </Column>

    <!-- Columna de Saldo Valorizado -->
    <Column field="saldo_valorizado" header="Saldo Valorizado" sortable style="min-width: 13rem">
      <template #body="{ data }">
        <div class="bg-green-50 px-3 py-2 rounded border border-green-200">
          <div class="flex items-center gap-1">
            <i class="pi pi-dollar text-green-600"></i>
            <span class="font-bold text-green-800 text-lg">S/ {{ data.saldo_valorizado }}</span>
          </div>
        </div>
      </template>
    </Column>

    <!-- Columnas adicionales -->
    <Column field="precio_venta" header="Precio Venta" sortable style="min-width: 11rem">
      <template #body="{ data }">
        <div class="flex items-center gap-1">
          <span class="text-xs text-gray-500">S/</span>
          <span class="font-semibold text-orange-600">{{ data.precio_venta }}</span>
        </div>
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

    <!-- Footer con totales -->
    <template #footer>
      <div class="flex justify-between items-center py-3 px-4 bg-gray-50 border-t-2 border-blue-500">
        <div class="flex gap-6">
          <div>
            <span class="text-sm text-gray-600">Total Registros:</span>
            <span class="ml-2 font-bold text-gray-800">{{ kardexValorizadoStore.pagination.total }}</span>
          </div>
        </div>
        <div class="flex gap-4">
          <div class="bg-blue-50 px-4 py-2 rounded border border-blue-200">
            <span class="text-sm text-blue-600">Costo Total de Movimientos:</span>
            <span class="ml-2 font-bold text-blue-800">S/ {{ calcularTotalCostos() }}</span>
          </div>
          <div class="bg-green-50 px-4 py-2 rounded border border-green-200">
            <span class="text-sm text-green-600">Saldo Valorizado Final:</span>
            <span class="ml-2 font-bold text-green-800 text-lg">S/ {{ calcularSaldoValorizado() }}</span>
          </div>
        </div>
      </div>
    </template>

  </DataTable>
</template>

<script setup>
import { ref } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup';
import Row from 'primevue/row';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import { useKardexValorizadoStore } from './kardexValorizadoStore';

const kardexValorizadoStore = useKardexValorizadoStore();

const dt = ref();
const selectedRecords = ref();
const filters = ref({
  'global': {value: null, matchMode: FilterMatchMode.CONTAINS},
});

// Función para obtener las clases de color según la cantidad
const getCantidadClass = (cantidad) => {
  const value = parseFloat(cantidad);
  if (value > 0) return 'text-green-600';
  if (value < 0) return 'text-red-600';
  return 'text-gray-600';
};

// Función para calcular el total de costos
const calcularTotalCostos = () => {
  if (!kardexValorizadoStore.kardexData || kardexValorizadoStore.kardexData.length === 0) {
    return '0.00';
  }
  
  const total = kardexValorizadoStore.kardexData.reduce((sum, item) => {
    // Remover comas y convertir a número
    const costo = parseFloat(item.costo_total.replace(/,/g, ''));
    return sum + (isNaN(costo) ? 0 : costo);
  }, 0);
  
  return total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
};

// Función para calcular el saldo valorizado (del último registro)
const calcularSaldoValorizado = () => {
  if (!kardexValorizadoStore.kardexData || kardexValorizadoStore.kardexData.length === 0) {
    return '0.00';
  }
  
  // El último registro tiene el saldo valorizado actual
  const ultimoRegistro = kardexValorizadoStore.kardexData[kardexValorizadoStore.kardexData.length - 1];
  return ultimoRegistro.saldo_valorizado;
};

// Función para exportar datos
const exportData = () => {
  dt.value.exportCSV();
};
</script>
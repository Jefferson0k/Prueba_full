<template>
  <DataTable 
    ref="dt" 
    v-model:selection="selectedProducts" 
    :value="pagosStore.pagos.value" 
    dataKey="id" 
    :loading="pagosStore.loading.value"
    :paginator="true" 
    :rows="15"
    :filters="filters"
    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
    :rowsPerPageOptions="[10, 15, 25, 50]"
    currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} pagos" 
    class="p-datatable-sm"
  >
    <template #header>
      <div class="flex flex-wrap gap-2 items-center justify-between">
        <h4 class="m-0 font-semibold text-xl">Listado de Pagos</h4>
        <div class="flex gap-2">
          <IconField>
            <InputIcon>
              <i class="pi pi-search" />
            </InputIcon>
            <InputText v-model="filters['global'].value" placeholder="Buscar..." />
          </IconField>
          <Button 
            icon="pi pi-refresh" 
            @click="loadPagos" 
            outlined 
            severity="contrast"
            :loading="pagosStore.loading.value"
            v-tooltip.top="'Actualizar'"
          />
        </div>
      </div>
    </template>

    <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>
    
    <Column field="empleado" header="Empleado" sortable style="min-width: 200px">
      <template #body="slotProps">
        <div class="flex flex-col">
          <span class="font-semibold">{{ slotProps.data.empleado }}</span>
        </div>
      </template>
    </Column>

    <Column field="monto_formateado" header="Monto" sortable style="min-width: 120px">
      <template #body="slotProps">
        <span class="font-semibold text-green-600">{{ slotProps.data.monto_formateado }}</span>
      </template>
    </Column>

    <Column field="fecha_pago_formateada" header="Fecha Pago" sortable style="min-width: 120px"></Column>
    <Column field="periodo" header="Periodo" sortable style="min-width: 120px"></Column>
    
    <Column field="tipo_pago" header="Tipo" sortable style="min-width: 120px">
      <template #body="slotProps">
        <Tag :value="slotProps.data.tipo_pago" severity="info" />
      </template>
    </Column>

    <Column field="metodo_pago" header="Método" sortable style="min-width: 120px">
      <template #body="slotProps">
        <Tag :value="slotProps.data.metodo_pago" />
      </template>
    </Column>

    <Column field="estado" header="Estado" sortable style="min-width: 100px">
      <template #body="slotProps">
        <Tag 
          :value="slotProps.data.estado" 
          :severity="getEstadoSeverity(slotProps.data.estado)"
        />
      </template>
    </Column>

    <Column header="Comprobante" style="min-width: 120px">
      <template #body="slotProps">
        <div v-if="slotProps.data.tiene_comprobante" class="flex gap-2">
          <Button 
            icon="pi pi-eye"
            rounded
            text
            severity="info"
            size="small"
            @click="verComprobante(slotProps.data)"
            v-tooltip.top="'Ver comprobante'"
          />
          <Button 
            icon="pi pi-download"
            rounded
            text
            severity="secondary"
            size="small"
            @click="descargarComprobante(slotProps.data)"
            v-tooltip.top="'Descargar'"
          />
        </div>
        <span v-else class="text-gray-400 text-sm">Sin comprobante</span>
      </template>
    </Column>

    <Column :exportable="false" style="min-width: 120px" header="Acciones">
      <template #body="slotProps">
        <div class="flex gap-2">
          <Button 
            icon="pi pi-trash" 
            outlined 
            rounded 
            severity="danger" 
            size="small"
            @click="confirmDeleteProduct(slotProps.data)" 
          />
        </div>
      </template>
    </Column>

    <template #empty>
      <div class="text-center py-8">
        <i class="pi pi-inbox text-4xl text-gray-400 mb-3"></i>
        <p class="text-gray-500">No se encontraron pagos registrados.</p>
      </div>
    </template>
  </DataTable>

  <Dialog 
    v-model:visible="comprobanteDialog" 
    :style="{ width: '800px' }" 
    header="Comprobante de Pago" 
    :modal="true"
  >
    <div v-if="comprobanteSeleccionado">
      <div v-if="comprobanteSeleccionado.tipo_comprobante === 'imagen'" class="text-center">
        <img 
          :src="comprobanteSeleccionado.comprobante_url" 
          alt="Comprobante"
          class="max-w-full h-auto rounded-lg shadow-lg"
        />
      </div>
      
      <div v-else-if="comprobanteSeleccionado.tipo_comprobante === 'pdf'" class="h-[600px]">
        <iframe 
          :src="comprobanteSeleccionado.comprobante_url"
          class="w-full h-full border-0 rounded-lg"
        ></iframe>
      </div>
    </div>
    
    <template #footer>
      <Button 
        label="Descargar" 
        icon="pi pi-download" 
        @click="descargarComprobante(comprobanteSeleccionado)"
        outlined
      />
      <Button label="Cerrar" icon="pi pi-times" @click="comprobanteDialog = false" />
    </template>
  </Dialog>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import Dialog from 'primevue/dialog';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';
import { usePagosPersonal, type PagoPersonal } from './usePagosPersonal';

const toast = useToast();
const confirm = useConfirm();
const pagosStore = usePagosPersonal();

const dt = ref();
const selectedProducts = ref<PagoPersonal[]>([]);
const comprobanteDialog = ref(false);
const comprobanteSeleccionado = ref<PagoPersonal | null>(null);

const filters = ref({
  'global': { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const loadPagos = async () => {
  try {
    await pagosStore.fetchPagos();
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudieron cargar los pagos', life: 3000 });
  }
};

const getEstadoSeverity = (estado: string) => {
  const severities: Record<string, string> = {
    'pagado': 'success',
    'pendiente': 'warn',
    'anulado': 'danger'
  };
  return severities[estado] || 'info';
};

const verComprobante = (pago: PagoPersonal) => {
  comprobanteSeleccionado.value = pago;
  comprobanteDialog.value = true;
};

const descargarComprobante = (pago: PagoPersonal) => {
  if (pago.comprobante_url) {
    window.open(pago.comprobante_url, '_blank');
  }
};

const confirmDeleteProduct = (pago: PagoPersonal) => {
  confirm.require({
    message: `¿Estás seguro de que deseas eliminar el pago de ${pago.empleado} por ${pago.monto_formateado}?`,
    header: 'Confirmar eliminación',
    icon: 'pi pi-exclamation-triangle',
    acceptClass: 'p-button-danger',
    accept: () => deleteProduct(pago)
  });
};

const deleteProduct = async (pago: PagoPersonal) => {
  try {
    await pagosStore.deletePago(pago.id);
    toast.add({ severity: 'success', summary: 'Éxito', detail: 'Pago eliminado correctamente', life: 3000 });
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo eliminar el pago', life: 3000 });
  }
};

onMounted(() => {
  loadPagos();
});

defineExpose({
  loadPagos
});
</script>
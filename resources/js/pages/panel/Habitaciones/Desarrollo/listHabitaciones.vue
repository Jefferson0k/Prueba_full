<template>
    <!-- Sección de Totales -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Total General -->
        <Message severity="info" class="border-round">
            <template #icon>
                <i class="pi pi-wallet text-2xl"></i>
            </template>
            <div class="ml-3">
                <div class="text-500 font-medium">Total General</div>
                <div class="text-2xl font-bold text-900">S/. 0.00</div>
            </div>
        </Message>

        <!-- Efectivo -->
        <Message severity="success" class="border-round">
            <template #icon>
                <i class="pi pi-money-bill text-2xl"></i>
            </template>
            <div class="ml-3">
                <div class="text-500 font-medium">Efectivo</div>
                <div class="text-2xl font-bold text-900">S/. 0.00</div>
            </div>
        </Message>

        <!-- Tarjeta -->
        <Message severity="warn" class="border-round">
            <template #icon>
                <i class="pi pi-credit-card text-2xl"></i>
            </template>
            <div class="ml-3">
                <div class="text-500 font-medium">Tarjeta</div>
                <div class="text-2xl font-bold text-900">S/. 0.00</div>
            </div>
        </Message>
    </div>

    <!-- Tabla de Habitaciones -->
    <DataTable ref="dt" v-model:selection="selectedProducts" :value="products" dataKey="id" :paginator="true" :rows="10"
        :filters="filters"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[5, 10, 25]"
        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} habitaciones" 
        class="p-datatable-sm">
        
        <template #header>
            <div class="flex flex-wrap gap-2 items-center justify-between">
                <h4 class="m-0">Habitaciones</h4>
                <IconField>
                    <InputIcon>
                        <i class="pi pi-search" />
                    </InputIcon>
                    <InputText v-model="filters['global'].value" placeholder="Buscar..." />
                </IconField>
            </div>
        </template>
        
        <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>
        <Column field="code" header="Codigo" sortable style="min-width: 12rem"></Column>
        <Column field="name" header="Tipo" sortable style="min-width: 16rem"></Column>
        <Column field="name" header="Tarifa" sortable style="min-width: 16rem"></Column>
        <Column field="name" header="Moneda" sortable style="min-width: 16rem"></Column>
        <Column field="name" header="Sub. Total H." sortable style="min-width: 16rem"></Column>
        <Column field="name" header="Sub. Total P." sortable style="min-width: 16rem"></Column>
        <Column field="name" header="Monto" sortable style="min-width: 16rem"></Column>
        <Column field="name" header="Estado" sortable style="min-width: 16rem"></Column>
        <Column field="name" header="Creacion" sortable style="min-width: 16rem"></Column>
        <Column field="name" header="Creador Por" sortable style="min-width: 16rem"></Column>
        <Column field="name" header="Acciones" sortable style="min-width: 16rem"></Column>
    </DataTable>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Message from 'primevue/message';

const filters = ref({
    'global': { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const selectedProducts = ref();
const products = ref();
</script>
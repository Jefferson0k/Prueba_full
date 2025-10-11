<template>
    <Toolbar class="mb-6">
        <template #start>

        </template>
        <template #end>
            <Button label="Ir a Habitaciones" icon="pi pi-arrow-right" severity="contrast" @click="goToHabitaciones" />
        </template>
    </Toolbar>
    <DataTable ref="dt" v-model:selection="selectedProducts" :value="products" dataKey="id" :paginator="true" :rows="10"
        :filters="filters"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[5, 10, 25]"
        currentPageReportTemplate="Showing {first} to {last} of {totalRecords} products" class="p-datatable-sm">
        <template #header>
            <div class="flex flex-wrap gap-2 items-center justify-between">
                <h4 class="m-0">Manage Products</h4>
                <IconField>
                    <InputIcon>
                        <i class="pi pi-search" />
                    </InputIcon>
                    <InputText v-model="filters['global'].value" placeholder="Search..." />
                </IconField>
            </div>
        </template>
        <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>
        <Column field="code" header="Code" sortable style="min-width: 12rem"></Column>
        <Column field="name" header="Name" sortable style="min-width: 16rem"></Column>
    </DataTable>
</template>
<script setup>
import { ref } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';
import { router } from '@inertiajs/vue3';
import Toolbar from 'primevue/toolbar';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';

const filters = ref({
    'global': { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const selectedProducts = ref();
const products = ref();

const goToHabitaciones = () => {
    router.visit('/panel/aperturar');
};
</script>
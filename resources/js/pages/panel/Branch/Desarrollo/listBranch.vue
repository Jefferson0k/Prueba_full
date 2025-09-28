<template>
    <DataTable ref="dt" v-model:selection="selectedProducts" :value="products" dataKey="id" :paginator="true" :rows="10"
        :totalRecords="totalRecords" :filters="filters"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[5, 10, 25]"
        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} sucursales" class="p-datatable-sm">
        <template #header>
            <div class="flex flex-wrap gap-2 items-center justify-between">
                <div class="flex items-center gap-2">
                    <h4 class="m-0">
                        SUCURSALES
                        <Tag severity="contrast">{{ totalRecords }}</Tag>
                    </h4>
                </div>
                <IconField>
                    <InputIcon>
                        <i class="pi pi-search" />
                    </InputIcon>
                    <InputText v-model="filters['global'].value" placeholder="Buscar..." class="mr-2" />
                    <Button icon="pi pi-refresh" severity="contrast"  rounded variant="outlined" aria-label="Star" @click="fetchBranches"/>
                </IconField>
            </div>
        </template>
        <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>
        <Column field="name" header="Nombre" sortable style="min-width: 14rem"></Column>
        <Column field="creacion" header="Creacion" sortable style="min-width: 14rem"></Column>
        <Column field="update" header="Modificacion" sortable style="min-width: 7rem"></Column>
        <Column>
            <template #body="slotProps">
                <Button icon="pi pi-ellipsis-v" class="p-button-text p-button-rounded p-button-plain"
                    @click="toggleMenu($event, slotProps.data.id)" />
                <Menu :model="getMenuItems(slotProps.data)" :popup="true"
                    :ref="el => actionMenus[slotProps.data.id] = el" />
            </template>
        </Column>
    </DataTable>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue';
import axios from 'axios';
import { FilterMatchMode } from '@primevue/core/api';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Menu from 'primevue/menu';
import Tag from 'primevue/tag';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    refresh: {
        type: Number,
        default: 0
    }
});

const dt = ref();
const products = ref([]);
const selectedProducts = ref();
const totalRecords = ref(0);

const actionMenus = reactive({});

const filters = ref({
    'global': { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const fetchBranches = async () => {
    try {
        const response = await axios.get('/branches');
        products.value = response.data.data;
        totalRecords.value = response.data.total;
        console.log('Datos cargados:', response.data.data);
    } catch (error) {
        console.error("Error cargando sucursales:", error);
    }
};

onMounted(() => {
    fetchBranches();
});

watch(() => props.refresh, (newValue, oldValue) => {
    if (newValue !== oldValue) {
        console.log('Refrescando listado...');
        fetchBranches();
    }
});

const verDetalle = (row) => {
    router.visit(`/panel/branches/${row.id}/sub-branches`);
};

const editarSucursal = (row) => {
    console.log("Editar sucursal:", row);
};
const eliminarSucursal = (row) => {
    console.log("Eliminar sucursal:", row);
};

const getMenuItems = (row) => [
    { label: 'Ver detalle', icon: 'pi pi-eye', command: () => verDetalle(row) },
    { label: 'Editar', icon: 'pi pi-pencil', command: () => editarSucursal(row) },
    { label: 'Eliminar', icon: 'pi pi-trash', command: () => eliminarSucursal(row) },
];

const toggleMenu = (event, rowId) => {
    actionMenus[rowId].toggle(event);
};
</script>
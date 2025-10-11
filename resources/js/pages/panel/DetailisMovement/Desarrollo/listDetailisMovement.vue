<template>
    <DataTable ref="dt" v-model:selection="selectedProducts" :value="products" dataKey="id" :paginator="true" :rows="10"
        :filters="filters" :loading="loading"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[5, 10, 25]"
        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} productos" class="p-datatable-sm">
        <template #header>
            <div class="flex flex-wrap gap-2 items-center justify-between">
                <h4 class="m-0">Detalle del Movimiento</h4>
                <IconField>
                    <InputIcon>
                        <i class="pi pi-search" />
                    </InputIcon>
                    <InputText v-model="filters['global'].value" placeholder="Buscar..." />
                </IconField>
            </div>
        </template>

        <template #empty>
            <div class="text-center p-4">
                No hay productos en este movimiento
            </div>
        </template>

        <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>

        <Column field="product.name" header="Producto" sortable style="min-width: 16rem">
            <template #body="slotProps">
                <strong>{{ slotProps.data.product.name }}</strong>
            </template>
        </Column>

        <Column field="expiry_date" header="Fecha Vencimiento" sortable style="min-width: 12rem">
            <template #body="slotProps">
                <span v-if="slotProps.data.expiry_date">{{ slotProps.data.expiry_date }}</span>
                <span v-else class="text-gray-400">Sin vencimiento</span>
            </template>
        </Column>
        
        <Column field="boxes" header="Cajas" sortable style="min-width: 8rem">
            <template #body="slotProps">
                <span>{{ slotProps.data.boxes }}</span>
            </template>
        </Column>

        <Column field="units_per_box" header="Unidades/Caja" sortable style="min-width: 10rem">
            <template #body="slotProps">
                <span>{{ slotProps.data.units_per_box }}</span>
            </template>
        </Column>

        <Column header="Total Unidades" style="min-width: 10rem">
            <template #body="slotProps">
                <span class="font-semibold">{{ slotProps.data.boxes * slotProps.data.units_per_box }}</span>
            </template>
        </Column>

        <Column field="unit_price" header="Precio Unitario" sortable style="min-width: 12rem">
            <template #body="slotProps">
                <span>S/ {{ parseFloat(slotProps.data.unit_price).toFixed(2) }}</span>
            </template>
        </Column>

        <Column field="total_price" header="Precio Total" sortable style="min-width: 12rem">
            <template #body="slotProps">
                <span class="font-bold text-green-600">S/ {{ parseFloat(slotProps.data.total_price).toFixed(2) }}</span>
            </template>
        </Column>

        <Column header="Acciones" :exportable="false" style="min-width: 10rem">
            <template #body="slotProps">
                <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editProduct(slotProps.data)"
                    severity="info" />
                <Button icon="pi pi-trash" outlined rounded severity="danger"
                    @click="confirmDeleteProduct(slotProps.data)" />
            </template>
        </Column>

        <!-- Footer con totales usando ColumnGroup -->
            <ColumnGroup type="footer">
                <Row>
                    <Column footer="" :colspan="6" />
                    <Column footer="Subtotal:" footerStyle="text-align: right; font-weight: bold; padding-right: 1rem;" />
                    <Column :footer="`S/ ${subtotal.toFixed(2)}`" footerStyle="font-weight: bold; text-align: left;" />
                    <Column footer="" />
                </Row>
                <Row>
                    <Column footer="" :colspan="6" />
                    <Column footer="IGV (18%):" footerStyle="text-align: right; font-weight: bold; padding-right: 1rem;" />
                    <Column :footer="`S/ ${igvTotal.toFixed(2)}`" footerStyle="font-weight: bold; color: #3B82F6; text-align: left;" />
                    <Column footer="" />
                </Row>
                <Row>
                    <Column footer="" :colspan="6" />
                    <Column footer="Total:" footerStyle="text-align: right; font-weight: bold; padding-right: 1rem; solid #e5e7eb; padding-top: 0.5rem;" />
                    <Column :footer="`S/ ${totalGeneral.toFixed(2)}`" footerStyle="font-weight: bold; color: #10B981; text-align: left; solid #e5e7eb; padding-top: 0.5rem;" />
                    <Column footer="" />
                </Row>
            </ColumnGroup>
    </DataTable>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import axios from 'axios';

import ColumnGroup from 'primevue/columngroup';
import Row from 'primevue/row';

const toast = useToast();

const props = defineProps<{
    movement: any
}>();

const dt = ref();
const products = ref([]);
const selectedProducts = ref();
const loading = ref(false);
const filters = ref({
    'global': { value: null, matchMode: FilterMatchMode.CONTAINS },
});

// Computed para totales
const totalUnidades = computed(() => {
    return products.value.reduce((sum, product) => {
        return sum + (product.boxes * product.units_per_box);
    }, 0);
});

const subtotal = computed(() => {
    return products.value.reduce((sum, product) => {
        return sum + parseFloat(product.total_price);
    }, 0);
});

// Calcular IGV para un producto individual
const calcularIGV = (monto: number) => {
    return parseFloat(monto) * 0.18; // 18% de IGV
};

// IGV total
const igvTotal = computed(() => {
    return products.value.reduce((sum, product) => {
        return sum + calcularIGV(product.total_price);
    }, 0);
});

// Total general (subtotal + IGV)
const totalGeneral = computed(() => {
    return subtotal.value + igvTotal.value;
});

// Cargar detalles del movimiento
const loadMovementDetails = async () => {
    if (!props.movement?.data?.id) {
        console.error('No se encontró el ID del movimiento');
        return;
    }

    loading.value = true;

    try {
        const response = await axios.get(`/movement-detail/${props.movement.data.id}/details`);

        products.value = response.data.data;

        console.log('Detalles cargados:', products.value);

    } catch (error) {
        console.error('Error al cargar detalles:', error);

        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'No se pudieron cargar los detalles del movimiento',
            life: 3000
        });
    } finally {
        loading.value = false;
    }
};

// Editar producto
const editProduct = (product: any) => {
    console.log('Editar producto:', product);

    toast.add({
        severity: 'info',
        summary: 'Editar',
        detail: 'Funcionalidad de edición en desarrollo',
        life: 3000
    });
};

// Confirmar eliminación
const confirmDeleteProduct = (product: any) => {
    console.log('Eliminar producto:', product);

    toast.add({
        severity: 'warn',
        summary: 'Eliminar',
        detail: 'Funcionalidad de eliminación en desarrollo',
        life: 3000
    });
};

// Recargar detalles cuando se agregue un producto
const reloadDetails = () => {
    loadMovementDetails();
};

// Exponer método para que el padre pueda recargar
defineExpose({
    reloadDetails
});

// Cargar al montar
onMounted(() => {
    loadMovementDetails();
});

// Observar cambios en el movement (por si se actualiza desde el padre)
watch(() => props.movement, () => {
    loadMovementDetails();
}, { deep: true });
</script>
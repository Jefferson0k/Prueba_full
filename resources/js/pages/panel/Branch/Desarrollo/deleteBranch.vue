<template>
    <Dialog v-model:visible="visible" modal header="Eliminar Sucursal" :style="{ width: '25rem' }">
        <template #header>
            <div class="flex items-center gap-2">
                <i class="pi pi-exclamation-triangle text-red-500" style="font-size: 1.5rem"></i>
                <span class="font-bold">Confirmar Eliminación</span>
            </div>
        </template>
        
        <div class="flex flex-col gap-4">
            <p class="m-0">
                ¿Está seguro que desea eliminar la sucursal <strong>{{ branchData?.name }}</strong>?
            </p>
            <p class="text-sm text-gray-600 m-0">
                Esta acción no se puede deshacer.
            </p>
        </div>

        <template #footer>
            <Button label="Cancelar" severity="secondary" text @click="closeDialog" :disabled="loading" />
            <Button label="Eliminar" severity="danger" @click="confirmDelete" :loading="loading" />
        </template>
    </Dialog>
</template>

<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    visible: {
        type: Boolean,
        default: false
    },
    branchId: {
        type: String,
        default: null
    }
});

const emit = defineEmits(['update:visible', 'deleted']);

const toast = useToast();
const visible = ref(props.visible);
const loading = ref(false);
const branchData = ref(null);

watch(() => props.visible, (newVal) => {
    visible.value = newVal;
    if (newVal && props.branchId) {
        fetchBranchData();
    }
});

watch(visible, (newVal) => {
    emit('update:visible', newVal);
});

const fetchBranchData = async () => {
    try {
        const response = await axios.get(`/branches/${props.branchId}`);
        branchData.value = response.data.data;
    } catch (error) {
        console.error("Error cargando datos de sucursal:", error);
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'No se pudo cargar la información de la sucursal',
            life: 3000
        });
    }
};

const confirmDelete = async () => {
    loading.value = true;
    try {
        await axios.delete(`/branches/${props.branchId}`);
        
        toast.add({
            severity: 'success',
            summary: 'Éxito',
            detail: 'Sucursal eliminada correctamente',
            life: 3000
        });
        
        emit('deleted');
        closeDialog();
    } catch (error) {
        console.error("Error eliminando sucursal:", error);
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: error.response?.data?.message || 'No se pudo eliminar la sucursal',
            life: 3000
        });
    } finally {
        loading.value = false;
    }
};

const closeDialog = () => {
    visible.value = false;
    branchData.value = null;
};
</script>
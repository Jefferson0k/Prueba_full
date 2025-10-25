<template>
    <Dialog v-model:visible="visible" modal header="Eliminar Sub Sucursal" :style="{ width: '30rem' }">
        <template #header>
            <div class="flex items-center gap-2">
                <i class="pi pi-exclamation-triangle text-red-500" style="font-size: 1.5rem"></i>
                <span class="font-bold">Confirmar Eliminación</span>
            </div>
        </template>
        
        <div class="flex flex-col gap-4">
            <p class="m-0">
                ¿Está seguro que desea eliminar la sub sucursal <strong>{{ subBranchData?.name }}</strong>?
            </p>
            <div v-if="subBranchData">
                <div class="flex flex-col gap-2 text-sm">
                    <div><strong>Código:</strong> {{ subBranchData.code }}</div>
                    <div><strong>Dirección:</strong> {{ subBranchData.address }}</div>
                    <div><strong>Teléfono:</strong> {{ subBranchData.phone }}</div>
                </div>
            </div>
            <p class="text-sm text-red-600 m-0 font-semibold">
                ⚠️ Esta acción no se puede deshacer y eliminará todos los pisos y habitaciones asociadas.
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
    subBranchId: {
        type: String,
        default: null
    }
});

const emit = defineEmits(['update:visible', 'deleted']);

const toast = useToast();
const visible = ref(props.visible);
const loading = ref(false);
const subBranchData = ref(null);

watch(() => props.visible, (newVal) => {
    visible.value = newVal;
    if (newVal && props.subBranchId) {
        fetchSubBranchData();
    }
});

watch(visible, (newVal) => {
    emit('update:visible', newVal);
});

const fetchSubBranchData = async () => {
    try {
        const response = await axios.get(`/sub-branches/show/${props.subBranchId}`);
        subBranchData.value = response.data.data;
    } catch (error) {
        console.error("Error cargando datos de sub sucursal:", error);
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'No se pudo cargar la información de la sub sucursal',
            life: 3000
        });
    }
};

const confirmDelete = async () => {
    loading.value = true;
    try {
        await axios.delete(`/sub-branches/${props.subBranchId}`);
        
        toast.add({
            severity: 'success',
            summary: 'Éxito',
            detail: 'Sub sucursal eliminada correctamente',
            life: 3000
        });
        
        emit('deleted');
        closeDialog();
    } catch (error) {
        console.error("Error eliminando sub sucursal:", error);
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: error.response?.data?.message || 'No se pudo eliminar la sub sucursal',
            life: 3000
        });
    } finally {
        loading.value = false;
    }
};

const closeDialog = () => {
    visible.value = false;
    subBranchData.value = null;
};
</script>
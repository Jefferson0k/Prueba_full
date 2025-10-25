<template>
    <Dialog v-model:visible="visible" modal header="Editar Sucursal" :style="{ width: '35rem' }">
        <template #header>
            <div class="flex items-center gap-2">
                <i class="pi pi-pencil" style="font-size: 1.5rem"></i>
                <span class="font-bold">Editar Sucursal</span>
            </div>
        </template>
        
        <div class="flex flex-col gap-4">
            <div class="flex flex-col gap-2">
                <label for="name" class="font-semibold">Nombre de la Sucursal</label>
                <InputText 
                    id="name" 
                    v-model="form.name" 
                    placeholder="Ingrese el nombre de la sucursal"
                    :invalid="!!errors.name"
                />
                <small v-if="errors.name" class="text-red-500">{{ errors.name }}</small>
            </div>

            <div class="flex items-center gap-3">
                <label for="is_active" class="font-semibold">Estado</label>
                <ToggleSwitch v-model="form.is_active" inputId="is_active" />
                <span class="text-sm" :class="form.is_active ? 'text-green-600' : 'text-red-600'">
                    {{ form.is_active ? 'Activo' : 'Inactivo' }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <label class="font-semibold text-sm">Fecha de Creación</label>
                    <p class="text-sm m-0">{{ branchData?.creacion || '-' }}</p>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="font-semibold text-sm">Última Modificación</label>
                    <p class="text-sm m-0">{{ branchData?.update || '-' }}</p>
                </div>
            </div>
        </div>

        <template #footer>
            <Button label="Cancelar" severity="secondary" @click="closeDialog"  text />
            <Button label="Guardar Cambios" @click="updateBranch" severity="contrast" :loading="loading" />
        </template>
    </Dialog>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';
import axios from 'axios';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import ToggleSwitch from 'primevue/toggleswitch';
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

const emit = defineEmits(['update:visible', 'updated']);

const toast = useToast();
const visible = ref(props.visible);
const loading = ref(false);
const branchData = ref(null);

const form = reactive({
    name: '',
    is_active: true
});

const errors = reactive({
    name: ''
});

watch(() => props.visible, (newVal) => {
    visible.value = newVal;
    if (newVal && props.branchId) {
        fetchBranchData();
    } else {
        resetForm();
    }
});

watch(visible, (newVal) => {
    emit('update:visible', newVal);
});

const fetchBranchData = async () => {
    loading.value = true;
    try {
        const response = await axios.get(`/branches/${props.branchId}`);
        branchData.value = response.data.data;
        form.name = response.data.data.name;
        form.is_active = response.data.data.is_active;
    } catch (error) {
        console.error("Error cargando datos de sucursal:", error);
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'No se pudo cargar la información de la sucursal',
            life: 3000
        });
        closeDialog();
    } finally {
        loading.value = false;
    }
};

const validateForm = () => {
    errors.name = '';
    let isValid = true;

    if (!form.name || form.name.trim() === '') {
        errors.name = 'El nombre de la sucursal es requerido';
        isValid = false;
    } else if (form.name.trim().length < 3) {
        errors.name = 'El nombre debe tener al menos 3 caracteres';
        isValid = false;
    }

    return isValid;
};

const updateBranch = async () => {
    if (!validateForm()) {
        return;
    }

    loading.value = true;
    try {
        await axios.put(`/branches/${props.branchId}`, {
            name: form.name.trim(),
            is_active: form.is_active
        });
        
        toast.add({
            severity: 'success',
            summary: 'Éxito',
            detail: 'Sucursal actualizada correctamente',
            life: 3000
        });
        
        emit('updated');
        closeDialog();
    } catch (error) {
        console.error("Error actualizando sucursal:", error);
        
        if (error.response?.data?.errors) {
            Object.assign(errors, error.response.data.errors);
        }
        
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: error.response?.data?.message || 'No se pudo actualizar la sucursal',
            life: 3000
        });
    } finally {
        loading.value = false;
    }
};

const resetForm = () => {
    form.name = '';
    form.is_active = true;
    errors.name = '';
    branchData.value = null;
};

const closeDialog = () => {
    visible.value = false;
    resetForm();
};
</script>
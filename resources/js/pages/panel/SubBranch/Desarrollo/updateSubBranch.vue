<template>
    <Dialog v-model:visible="visible" modal header="Editar Sub Sucursal" :style="{ width: '40rem' }">
        <template #header>
            <div class="flex items-center gap-2">
                <i class="pi pi-pencil" style="font-size: 1.5rem"></i>
                <span class="font-bold">Editar Sub Sucursal</span>
            </div>
        </template>
        
        <div class="flex flex-col gap-4 py-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <label for="code" class="font-semibold">Código</label>
                    <InputText 
                        id="code" 
                        v-model="form.code" 
                        placeholder="Código de la sub sucursal"
                        :invalid="!!errors.code"
                        disabled
                        class="bg-gray-100"
                    />
                    <small v-if="errors.code" class="text-red-500">{{ errors.code }}</small>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="phone" class="font-semibold">Teléfono</label>
                    <InputText 
                        id="phone" 
                        v-model="form.phone" 
                        placeholder="Número de teléfono"
                        :invalid="!!errors.phone"
                    />
                    <small v-if="errors.phone" class="text-red-500">{{ errors.phone }}</small>
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <label for="name" class="font-semibold">Nombre</label>
                <InputText 
                    id="name" 
                    v-model="form.name" 
                    placeholder="Nombre de la sub sucursal"
                    :invalid="!!errors.name"
                />
                <small v-if="errors.name" class="text-red-500">{{ errors.name }}</small>
            </div>

            <div class="flex flex-col gap-2">
                <label for="address" class="font-semibold">Dirección</label>
                <Textarea 
                    id="address" 
                    v-model="form.address" 
                    placeholder="Dirección completa"
                    :invalid="!!errors.address"
                    rows="3"
                />
                <small v-if="errors.address" class="text-red-500">{{ errors.address }}</small>
            </div>

            <div class="flex items-center gap-3">
                <label for="is_active" class="font-semibold">Estado</label>
                <ToggleSwitch v-model="form.is_active" inputId="is_active" />
                <span class="text-sm" :class="form.is_active ? 'text-green-600' : 'text-red-600'">
                    {{ form.is_active ? 'Activo' : 'Inactivo' }}
                </span>
            </div>
        </div>

        <template #footer>
            <Button label="Cancelar" severity="secondary" text @click="closeDialog" :disabled="loading" />
            <Button label="Guardar Cambios" severity="contrast" @click="updateSubBranch" :loading="loading" />
        </template>
    </Dialog>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';
import axios from 'axios';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import { useToast } from 'primevue/usetoast';
import ToggleSwitch from 'primevue/toggleswitch';

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

const emit = defineEmits(['update:visible', 'updated']);

const toast = useToast();
const visible = ref(props.visible);
const loading = ref(false);

const form = reactive({
    name: '',
    code: '',
    address: '',
    phone: '',
    branch_id: '',
    is_active: true
});

const errors = reactive({
    name: '',
    code: '',
    address: '',
    phone: '',
    branch_id: ''
});

watch(() => props.visible, (newVal) => {
    visible.value = newVal;
    if (newVal && props.subBranchId) {
        fetchSubBranchData();
    } else {
        resetForm();
    }
});

watch(visible, (newVal) => {
    emit('update:visible', newVal);
});

const fetchSubBranchData = async () => {
    loading.value = true;
    try {
        const response = await axios.get(`/sub-branches/show/${props.subBranchId}`);
        const data = response.data.data;
        
        form.name = data.name;
        form.code = data.code;
        form.address = data.address;
        form.phone = data.phone;
        form.branch_id = data.branch_id;
        form.is_active = data.is_active;
        
        console.log('Datos cargados:', data);
        console.log('branch_id:', form.branch_id);
    } catch (error) {
        console.error("Error cargando datos de sub sucursal:", error);
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'No se pudo cargar la información de la sub sucursal',
            life: 3000
        });
        closeDialog();
    } finally {
        loading.value = false;
    }
};

const validateForm = () => {
    errors.name = '';
    errors.address = '';
    errors.phone = '';
    errors.branch_id = '';
    let isValid = true;

    if (!form.branch_id) {
        errors.branch_id = 'La sucursal principal es obligatoria';
        isValid = false;
    }

    if (!form.name || form.name.trim() === '') {
        errors.name = 'El nombre es requerido';
        isValid = false;
    } else if (form.name.trim().length < 3) {
        errors.name = 'El nombre debe tener al menos 3 caracteres';
        isValid = false;
    }

    if (!form.address || form.address.trim() === '') {
        errors.address = 'La dirección es requerida';
        isValid = false;
    }

    if (!form.phone || form.phone.trim() === '') {
        errors.phone = 'El teléfono es requerido';
        isValid = false;
    } else if (form.phone.trim().length < 7) {
        errors.phone = 'El teléfono debe tener al menos 7 caracteres';
        isValid = false;
    }

    return isValid;
};

const updateSubBranch = async () => {
    if (!validateForm()) {
        toast.add({
            severity: 'warn',
            summary: 'Validación',
            detail: 'Por favor complete todos los campos requeridos',
            life: 3000
        });
        return;
    }

    loading.value = true;
    
    const payload = {
        branch_id: form.branch_id,
        name: form.name.trim(),
        address: form.address.trim(),
        phone: form.phone.trim(),
        is_active: form.is_active
    };
    
    console.log('Payload a enviar:', payload);
    
    try {
        await axios.put(`/sub-branches/${props.subBranchId}`, payload);
        
        toast.add({
            severity: 'success',
            summary: 'Éxito',
            detail: 'Sub sucursal actualizada correctamente',
            life: 3000
        });
        
        emit('updated');
        closeDialog();
    } catch (error) {
        console.error("Error actualizando sub sucursal:", error);
        console.error("Response data:", error.response?.data);
        
        if (error.response?.data?.errors) {
            Object.assign(errors, error.response.data.errors);
        }
        
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: error.response?.data?.message || 'No se pudo actualizar la sub sucursal',
            life: 3000
        });
    } finally {
        loading.value = false;
    }
};

const resetForm = () => {
    form.name = '';
    form.code = '';
    form.address = '';
    form.phone = '';
    form.branch_id = '';
    form.is_active = true;
    errors.name = '';
    errors.code = '';
    errors.address = '';
    errors.phone = '';
    errors.branch_id = '';
};

const closeDialog = () => {
    visible.value = false;
    resetForm();
};
</script>
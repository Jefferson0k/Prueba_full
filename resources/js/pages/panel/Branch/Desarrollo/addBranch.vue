<template>
    <Toolbar class="mb-6">
        <template #start>
            <Button label="Nueva Sucursal" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openNew" />
        </template>
    </Toolbar>

    <Dialog v-model:visible="branchDialog" :style="{ width: '600px' }" header="Registro de Sucursales" :modal="true">
        <div class="flex flex-col gap-6">
            <div class="grid grid-cols-12 gap-4">
                <!-- Nombre -->
                <div class="col-span-12">
                    <label for="name" class="block font-bold mb-2">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <InputText id="name" v-model.trim="branch.name" maxlength="255" fluid
                        :class="{ 'p-invalid': submitted && (!branch.name || serverErrors.name) }" />
                    <small v-if="submitted && !branch.name" class="text-red-500">El nombre es obligatorio.</small>
                    <small v-else-if="serverErrors.name" class="text-red-500">{{ serverErrors.name[0] }}</small>
                </div>
                <!-- Estado -->
                <div class="col-span-12">
                    <label class="block font-bold mb-2">Estado <span class="text-red-500">*</span></label>
                    <Select v-model="branch.is_active" :options="estadoOptions" optionLabel="label" optionValue="value"
                        class="w-full" />
                    <small v-if="serverErrors.is_active" class="text-red-500">{{ serverErrors.is_active[0] }}</small>
                </div>
            </div>
        </div>

        <template #footer>
            <div class="flex justify-between items-center w-full">
                <!-- Leyenda de campos requeridos -->
                <small>
                    <span class="text-red-500">*</span> Campos obligatorios
                </small>
                
                <!-- Botones -->
                <div class="flex gap-2">
                    <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
                    <Button label="Guardar" icon="pi pi-check" :loading="loading" :disabled="!branch.name || loading"
                        @click="guardarBranch" />
                </div>
            </div>
        </template>
    </Dialog>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import Toolbar from 'primevue/toolbar';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import { useToast } from 'primevue/usetoast';
import { defineEmits } from 'vue';

const emit = defineEmits(['agregado']);
const toast = useToast();

const branchDialog = ref(false);
const submitted = ref(false);
const loading = ref(false);
const serverErrors = ref({});

const branch = ref({
    name: '',
    is_active: true,
});

const estadoOptions = [
    { label: 'Activo', value: true },
    { label: 'Inactivo', value: false },
];

function resetBranch() {
    branch.value = {
        name: '',
        is_active: true,
    };
    serverErrors.value = {};
    submitted.value = false;
    loading.value = false;
}

function openNew() {
    resetBranch();
    branchDialog.value = true;
}

function hideDialog() {
    branchDialog.value = false;
    resetBranch();
}

async function guardarBranch() {
    submitted.value = true;
    serverErrors.value = {};
    loading.value = true;

    try {
        const response = await axios.post('/branches', branch.value);
        toast.add({
            severity: 'success',
            summary: 'Éxito',
            detail: 'Sucursal registrada correctamente',
            life: 3000,
        });
        hideDialog();
        emit('agregado');
    } catch (error) {
        if (error.response && error.response.status === 422) {
            serverErrors.value = error.response.data.errors || {};
        } else {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'No se pudo registrar la sucursal',
                life: 3000,
            });
        }
    } finally {
        loading.value = false;
    }
}
</script>
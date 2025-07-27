<template>
    <Toolbar class="mb-6">
        <template #start>
            <Button label="Nueva categoria" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openNew" />
        </template>
    </Toolbar>
    
    <Dialog v-model:visible="categoriaDialog" :style="{ width: '600px' }" header="Registro de Categoría" :modal="true">
        <div class="flex flex-col gap-6">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-9">
                    <label for="nombre" class="block font-bold mb-3">Nombre <span class="text-red-500">*</span></label>
                    <InputText id="nombre" v-model.trim="categoria.nombre" required maxlength="100" fluid />
                    <small v-if="submitted && !categoria.nombre" class="text-red-500">El nombre es obligatorio.</small>
                    <small v-else-if="submitted && categoria.nombre && categoria.nombre.length < 2" class="text-red-500">
                        El nombre debe tener al menos 2 caracteres.
                    </small>
                    <small v-else-if="serverErrors.nombre" class="text-red-500">{{ serverErrors.nombre[0] }}</small>
                </div>
                <div class="col-span-3">
                    <label for="estado" class="block font-bold mb-2">Estado <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-3">
                        <Checkbox v-model="categoria.estado" :binary="true" inputId="estado" />
                        <Tag :value="categoria.estado ? 'Activo' : 'Inactivo'" :severity="categoria.estado ? 'success' : 'danger'" />
                        <small v-if="submitted && categoria.estado === null" class="text-red-500">El estado es obligatorio.</small>
                        <small v-else-if="serverErrors.estado" class="text-red-500">{{ serverErrors.estado[0] }}</small>
                    </div>
                </div>
            </div>
        </div>

        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
            <Button label="Guardar" icon="pi pi-check" @click="guardarCategoria" />
        </template>
    </Dialog>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import Toolbar from 'primevue/toolbar';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Checkbox from 'primevue/checkbox';
import Tag from 'primevue/tag';
import { useToast } from 'primevue/usetoast';
import { defineEmits } from 'vue';

const toast = useToast();
const submitted = ref(false);
const categoriaDialog = ref(false);
const serverErrors = ref({});
const emit = defineEmits(['categoria-agregada']);

const categoria = ref({
    nombre: '',
    estado: true
});

function resetCategoria() {
    categoria.value = {
        nombre: '',
        estado: true
    };
    serverErrors.value = {};
    submitted.value = false;
}

function openNew() {
    resetCategoria();
    categoriaDialog.value = true;
}

function hideDialog() {
    categoriaDialog.value = false;
    resetCategoria();
}

function guardarCategoria() {
    submitted.value = true;
    serverErrors.value = {};

    axios.post('/categoria', categoria.value)
        .then(() => {
            toast.add({ severity: 'success', summary: 'Éxito', detail: 'Categoría registrada', life: 3000 });
            hideDialog();
            emit('categoria-agregada');
        })
        .catch(error => {
            if (error.response && error.response.status === 422) {
                serverErrors.value = error.response.data.errors || {};
            } else {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'No se pudo registrar la categoría',
                    life: 3000
                });
            }
        });
}
</script>

<template>
    <Toolbar class="mb-6">
        <template #start>
            <Button label="Nuevo producto" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openNew" />
        </template>
    </Toolbar>

    <Dialog v-model:visible="productoDialog" :style="{ width: '700px' }" header="Registro de productos" :modal="true">
        <div class="flex flex-col gap-6">
            <div class="grid grid-cols-12 gap-4">
                <!-- Nombre y Estado en una sola fila -->
                <div class="col-span-12 grid grid-cols-12 gap-4">
                    <!-- Nombre -->
                    <div class="col-span-10">
                        <label class="block font-bold mb-2">Nombre <span class="text-red-500">*</span></label>
                        <InputText v-model.trim="producto.nombre" fluid maxlength="100" />
                        <small v-if="submitted && !producto.nombre" class="text-red-500">El nombre es obligatorio.</small>
                        <small v-else-if="submitted && producto.nombre.length < 2" class="text-red-500">El nombre debe tener al menos 2 caracteres.</small>
                        <small v-else-if="serverErrors.nombre" class="text-red-500">{{ serverErrors.nombre[0] }}</small>
                    </div>
                    <!-- Estado -->
                    <div class="col-span-2">
                        <label class="block font-bold mb-2">Estado <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-2">
                            <Checkbox v-model="producto.estado" :binary="true" />
                            <Tag :value="producto.estado ? 'Activo' : 'Inactivo'" :severity="producto.estado ? 'success' : 'danger'" />
                        </div>
                        <small v-if="submitted && producto.estado === null" class="text-red-500">El estado es obligatorio.</small>
                        <small v-else-if="serverErrors.estado" class="text-red-500">{{ serverErrors.estado[0] }}</small>
                    </div>
                </div>
                <!-- Precio Compra -->
                <div class="col-span-6">
                    <label class="block font-bold mb-2">Precio Compra <span class="text-red-500">*</span></label>
                    <InputText v-model.number="producto.precio_compra" type="number" fluid min="0" />
                    <small v-if="submitted && (producto.precio_compra === null || producto.precio_compra === '')" class="text-red-500">El precio de compra es obligatorio.</small>
                    <small v-else-if="serverErrors.precio_compra" class="text-red-500">{{ serverErrors.precio_compra[0] }}</small>
                </div>

                <!-- Precio Venta -->
                <div class="col-span-6">
                    <label class="block font-bold mb-2">Precio Venta <span class="text-red-500">*</span></label>
                    <InputText v-model.number="producto.precio_venta" fluid type="number" min="0" />
                    <small v-if="submitted && (producto.precio_venta === null || producto.precio_venta === '')" class="text-red-500">El precio de venta es obligatorio.</small>
                    <small v-else-if="serverErrors.precio_venta" class="text-red-500">{{ serverErrors.precio_venta[0] }}</small>
                </div>
                <!-- Categoría -->
                <div class="col-span-12">
                    <label class="block font-bold mb-2">Categoría <span class="text-red-500">*</span></label>
                    <Select v-model="producto.categoria_id" :options="categorias" fluid optionLabel="label" optionValue="value" placeholder="Seleccione categoría" />
                    <small v-if="submitted && !producto.categoria_id" class="text-red-500">La categoría es obligatoria.</small>
                    <small v-else-if="serverErrors.categoria_id" class="text-red-500">{{ serverErrors.categoria_id[0] }}</small>
                </div>
            </div>
        </div>
        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
            <Button label="Guardar" icon="pi pi-check" @click="guardarProducto" />
        </template>
    </Dialog>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import Dialog from 'primevue/dialog';
import Toolbar from 'primevue/toolbar';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Checkbox from 'primevue/checkbox';
import Tag from 'primevue/tag';
import { useToast } from 'primevue/usetoast';
import Select from 'primevue/select';

const toast = useToast();
const submitted = ref(false);
const productoDialog = ref(false);
const serverErrors = ref({});
const emit = defineEmits(['producto-agregado']);

const producto = ref({
    nombre: '',
    estado: true,
    precio_compra: null,
    precio_venta: null,
    categoria_id: null,
});

const categorias = ref([]);

function resetProducto() {
    producto.value = {
        nombre: '',
        estado: true,
        precio_compra: null,
        precio_venta: null,
        categoria_id: null,
    };
    serverErrors.value = {};
    submitted.value = false;
}

function openNew() {
    resetProducto();
    fetchCategorias();
    productoDialog.value = true;
}

function hideDialog() {
    productoDialog.value = false;
    resetProducto();
}

async function fetchCategorias() {
    try {
        const { data } = await axios.get('/categoria');
        categorias.value = data.data.map(c => ({ label: c.nombre, value: c.id }));
    } catch (e) {
        toast.add({ severity: 'warn', summary: 'Advertencia', detail: 'No se pudieron cargar categorías' });
    }
}

function guardarProducto() {
    submitted.value = true;
    serverErrors.value = {};

    axios.post('/producto', producto.value)
        .then(() => {
            toast.add({ severity: 'success', summary: 'Éxito', detail: 'Producto registrado', life: 3000 });
            hideDialog();
            emit('producto-agregado');
        })
        .catch(error => {
            if (error.response?.status === 422) {
                serverErrors.value = error.response.data.errors || {};
            } else {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'No se pudo registrar el producto',
                    life: 3000
                });
            }
        });
}
</script>

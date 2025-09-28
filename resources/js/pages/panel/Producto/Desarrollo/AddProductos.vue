<template>
    <Toolbar class="mb-6">
        <template #start>
            <Button label="Nuevo producto" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openNew" />
        </template>
    </Toolbar>

    <Dialog v-model:visible="productoDialog" :style="{ width: '800px' }" header="Registro de productos" :modal="true">
        <div class="flex flex-col gap-6">
            <div class="grid grid-cols-12 gap-4">
                <!-- Nombre y Estado en una sola fila -->
                <div class="col-span-12 grid grid-cols-12 gap-4">
                    <!-- Nombre -->
                    <div class="col-span-10">
                        <label class="block font-bold mb-2">Nombre <span class="text-red-500">*</span></label>
                        <InputText v-model.trim="producto.name" fluid maxlength="100" />
                        <small v-if="submitted && !producto.name" class="text-red-500">El nombre es obligatorio.</small>
                        <small v-else-if="submitted && producto.name.length < 2" class="text-red-500">El nombre debe tener al menos 2 caracteres.</small>
                        <small v-else-if="serverErrors.name" class="text-red-500">{{ serverErrors.name[0] }}</small>
                    </div>
                    <!-- Estado -->
                    <div class="col-span-2">
                        <label class="block font-bold mb-2">Estado <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-2">
                            <Checkbox v-model="producto.is_active" :binary="true" />
                            <Tag :value="producto.is_active ? 'Activo' : 'Inactivo'" :severity="producto.is_active ? 'success' : 'danger'" />
                        </div>
                        <small v-if="submitted && producto.is_active === null" class="text-red-500">El estado es obligatorio.</small>
                        <small v-else-if="serverErrors.is_active" class="text-red-500">{{ serverErrors.is_active[0] }}</small>
                    </div>
                </div>
                
                <!-- Precio Compra -->
                <div class="col-span-6">
                    <label class="block font-bold mb-2">Precio Compra <span class="text-red-500">*</span></label>
                    <InputText v-model.number="producto.purchase_price" type="number" fluid min="0" step="0.01" />
                    <small v-if="submitted && (producto.purchase_price === null || producto.purchase_price === '')" class="text-red-500">El precio de compra es obligatorio.</small>
                    <small v-else-if="serverErrors.purchase_price" class="text-red-500">{{ serverErrors.purchase_price[0] }}</small>
                </div>

                <!-- Precio Venta -->
                <div class="col-span-6">
                    <label class="block font-bold mb-2">Precio Venta <span class="text-red-500">*</span></label>
                    <InputText v-model.number="producto.sale_price" fluid type="number" min="0" step="0.01" />
                    <small v-if="submitted && (producto.sale_price === null || producto.sale_price === '')" class="text-red-500">El precio de venta es obligatorio.</small>
                    <small v-else-if="serverErrors.sale_price" class="text-red-500">{{ serverErrors.sale_price[0] }}</small>
                </div>

                <!-- Categoría -->
                <div class="col-span-6">
                    <label class="block font-bold mb-2">Categoría <span class="text-red-500">*</span></label>
                    <Select v-model="producto.category_id" :options="categorias" fluid optionLabel="label" optionValue="value" placeholder="Seleccione categoría" />
                    <small v-if="submitted && !producto.category_id" class="text-red-500">La categoría es obligatoria.</small>
                    <small v-else-if="serverErrors.category_id" class="text-red-500">{{ serverErrors.category_id[0] }}</small>
                </div>

                <!-- Tipo de Unidad -->
                <div class="col-span-6">
                    <label class="block font-bold mb-2">Tipo de Unidad <span class="text-red-500">*</span></label>
                    <Select v-model="producto.unit_type" :options="tiposUnidad" fluid optionLabel="label" optionValue="value" placeholder="Seleccione tipo de unidad" />
                    <small v-if="submitted && !producto.unit_type" class="text-red-500">El tipo de unidad es obligatorio.</small>
                    <small v-else-if="serverErrors.unit_type" class="text-red-500">{{ serverErrors.unit_type[0] }}</small>
                </div>

                <!-- Descripción -->
                <div class="col-span-12">
                    <label class="block font-bold mb-2">Descripción</label>
                    <Textarea v-model="producto.description" fluid rows="3" maxlength="1000" placeholder="Descripción del producto (opcional)" />
                    <small v-if="serverErrors.description" class="text-red-500">{{ serverErrors.description[0] }}</small>
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
import Select from 'primevue/select';
import Textarea from 'primevue/textarea';
import { useToast } from 'primevue/usetoast';

const toast = useToast();
const submitted = ref(false);
const productoDialog = ref(false);
const serverErrors = ref({});
const emit = defineEmits(['producto-agregado']);

const producto = ref({
    name: '',
    is_active: true,
    purchase_price: null,
    sale_price: null,
    category_id: null,
    unit_type: null,
    description: '',
});

const categorias = ref([]);

const tiposUnidad = ref([
    { label: 'Unidad', value: 'piece' },
    { label: 'Botella', value: 'bottle' },
    { label: 'Paquete', value: 'pack' },
    { label: 'Kilogramo', value: 'kg' },
    { label: 'Litro', value: 'liter' },
]);

function resetProducto() {
    producto.value = {
        name: '',
        is_active: true,
        purchase_price: null,
        sale_price: null,
        category_id: null,
        unit_type: null,
        description: '',
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
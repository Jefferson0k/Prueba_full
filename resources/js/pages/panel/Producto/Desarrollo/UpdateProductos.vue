<script setup>
import { ref, watch } from 'vue';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import Select from 'primevue/select';
import Tag from 'primevue/tag';
import Textarea from 'primevue/textarea';
import { useToast } from 'primevue/usetoast';
import axios from 'axios';

const props = defineProps({
    visible: Boolean,
    productoId: String
});
const emit = defineEmits(['update:visible', 'updated']);

const toast = useToast();
const serverErrors = ref({});
const submitted = ref(false);
const loading = ref(false);

const dialogVisible = ref(props.visible);
watch(() => props.visible, (val) => dialogVisible.value = val);
watch(dialogVisible, (val) => emit('update:visible', val));

const producto = ref({
    name: '',
    category_id: null,
    is_active: false,
    price: null,
    description: '',
});

const categorias = ref([]);

watch(() => props.visible, async (val) => {
    if (val && props.productoId) {
        await fetchProducto();
        await fetchCategorias();
    }
});

const fetchProducto = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get(`/producto/${props.productoId}`);
        const p = data.product;

        producto.value = {
            name: p.name || p.nombre,
            category_id: p.category_id || p.categoria_id,
            is_active: p.is_active !== undefined ? p.is_active : p.estado,
            price: parseFloat(p.price || p.precio),
            description: p.description || p.descripcion || '',
        };
    } catch {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar el producto', life: 3000 });
    } finally {
        loading.value = false;
    }
};

const fetchCategorias = async () => {
    try {
        const { data } = await axios.get('/categoria');
        categorias.value = data.data.map(c => ({ label: c.nombre, value: c.id }));
    } catch {
        toast.add({ severity: 'warn', summary: 'Advertencia', detail: 'No se pudieron cargar las categorías' });
    }
};

const updateProducto = async () => {
    submitted.value = true;
    serverErrors.value = {};

    // Preparar datos para envío
    const dataToSend = { ...producto.value };

    try {
        await axios.put(`/producto/${props.productoId}`, dataToSend);

        toast.add({
            severity: 'success',
            summary: 'Actualizado',
            detail: 'Producto actualizado correctamente',
            life: 3000
        });

        dialogVisible.value = false;
        emit('updated');
    } catch (error) {
        if (error.response?.data?.errors) {
            serverErrors.value = error.response.data.errors;
            toast.add({
                severity: 'error',
                summary: 'Error de validación',
                detail: error.response.data.message || 'Revisa los campos.',
                life: 5000
            });
        } else {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'No se pudo actualizar el producto',
                life: 3000
            });
        }
    }
};
</script>

<template>
    <Dialog v-model:visible="dialogVisible" header="Editar Producto" modal :closable="true" :closeOnEscape="true"
        :style="{ width: '900px' }">
        <div class="flex flex-col gap-6">
            <div class="grid grid-cols-12 gap-4">
                <!-- Nombre y Estado en una sola fila -->
                <div class="col-span-12 grid grid-cols-12 gap-4">
                    <!-- Nombre -->
                    <div class="col-span-10">
                        <label class="block font-bold mb-2">Nombre <span class="text-red-500">*</span></label>
                        <InputText v-model="producto.name" maxlength="100" fluid
                            :class="{ 'p-invalid': submitted && serverErrors.name }" />
                        <small v-if="serverErrors.name" class="text-red-500">{{ serverErrors.name[0] }}</small>
                    </div>
                    <!-- Estado -->
                    <div class="col-span-2">
                        <label class="block font-bold mb-2">Estado <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-2">
                            <Checkbox v-model="producto.is_active" :binary="true" />
                            <Tag :value="producto.is_active ? 'Activo' : 'Inactivo'"
                                :severity="producto.is_active ? 'success' : 'danger'" />
                        </div>
                        <small v-if="serverErrors.is_active" class="text-red-500">{{ serverErrors.is_active[0] }}</small>
                    </div>
                </div>

                <!-- Precio -->
                <div class="col-span-6">
                    <label class="block font-bold mb-2">Precio <span class="text-red-500">*</span></label>
                    <InputNumber v-model="producto.price" mode="currency" currency="PEN" locale="es-PE"
                        :minFractionDigits="2" fluid
                        :class="{ 'p-invalid': submitted && serverErrors.price }" />
                    <small v-if="serverErrors.price" class="text-red-500">{{ serverErrors.price[0] }}</small>
                </div>

                <!-- Categoría -->
                <div class="col-span-6">
                    <label class="block font-bold mb-2">Categoría <span class="text-red-500">*</span></label>
                    <Select v-model="producto.category_id" :options="categorias" optionLabel="label" optionValue="value"
                        placeholder="Seleccione una categoría" fluid
                        :class="{ 'p-invalid': submitted && serverErrors.category_id }" />
                    <small v-if="serverErrors.category_id" class="text-red-500">{{ serverErrors.category_id[0] }}</small>
                </div>

                <!-- Descripción -->
                <div class="col-span-12">
                    <label class="block font-bold mb-2">Descripción</label>
                    <Textarea v-model="producto.description" fluid rows="3" maxlength="1000" 
                        placeholder="Descripción del producto (opcional)"
                        :class="{ 'p-invalid': submitted && serverErrors.description }" />
                    <small v-if="serverErrors.description" class="text-red-500">{{ serverErrors.description[0] }}</small>
                </div>
            </div>
        </div>

        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" severity="secondary" text @click="dialogVisible = false" />
            <Button label="Guardar" icon="pi pi-check" severity="contrast" @click="updateProducto" :loading="loading" />
        </template>
    </Dialog>
</template>
<script setup>
import { ref, watch, onMounted } from 'vue';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import Select from 'primevue/select';
import Tag from 'primevue/tag';
import { useToast } from 'primevue/usetoast';
import axios from 'axios';

const props = defineProps({
    visible: Boolean,
    productoId: Number
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
    nombre: '',
    categoria_id: null,
    estado: false,
    precio_compra: null,
    precio_venta: null
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
            nombre: p.nombre,
            categoria_id: p.categoria_id,
            estado: true,
            precio_compra: parseFloat(p.precio_compra),
            precio_venta: parseFloat(p.precio_venta),
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

    try {
        await axios.put(`/producto/${props.productoId}`, { ...producto.value });

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
        :style="{ width: '700px' }">
        <div class="flex flex-col gap-6">
            <div class="grid grid-cols-12 gap-4">
                <!-- Nombre -->
                <div class="col-span-10">
                    <label class="block font-bold mb-2">Nombre <span class="text-red-500">*</span></label>
                    <InputText v-model="producto.nombre" maxlength="100" fluid
                        :class="{ 'p-invalid': submitted && serverErrors.nombre }" />
                    <small v-if="serverErrors.nombre" class="text-red-500">{{ serverErrors.nombre[0] }}</small>
                </div>

                <!-- Estado -->
                <div class="col-span-2">
                    <label class="block font-bold mb-2">Estado <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-3">
                        <Checkbox v-model="producto.estado" :binary="true" fluid />
                        <Tag :value="producto.estado ? 'Activo' : 'Inactivo'"
                            :severity="producto.estado ? 'success' : 'danger'" />
                    </div>
                    <small v-if="serverErrors.estado" class="text-red-500">{{ serverErrors.estado[0] }}</small>
                </div>

                <!-- Categoría -->
                <div class="col-span-6">
                    <label class="block font-bold mb-2">Categoría <span class="text-red-500">*</span></label>
                    <Select v-model="producto.categoria_id" :options="categorias" optionLabel="label" optionValue="value"
                        placeholder="Seleccione una categoría" fluid
                        :class="{ 'p-invalid': submitted && serverErrors.categoria_id }" />
                    <small v-if="serverErrors.categoria_id" class="text-red-500">{{ serverErrors.categoria_id[0] }}</small>
                </div>

                <!-- Precio Compra -->
                <div class="col-span-3">
                    <label class="block font-bold mb-2">Precio Compra <span class="text-red-500">*</span></label>
                    <InputNumber v-model="producto.precio_compra" mode="currency" currency="PEN" locale="es-PE"
                        :minFractionDigits="2" fluid
                        :class="{ 'p-invalid': submitted && serverErrors.precio_compra }" />
                    <small v-if="serverErrors.precio_compra" class="text-red-500">{{ serverErrors.precio_compra[0] }}</small>
                </div>

                <!-- Precio Venta -->
                <div class="col-span-3">
                    <label class="block font-bold mb-2">Precio Venta <span class="text-red-500">*</span></label>
                    <InputNumber v-model="producto.precio_venta" mode="currency" currency="PEN" locale="es-PE"
                        :minFractionDigits="2" fluid
                        :class="{ 'p-invalid': submitted && serverErrors.precio_venta }" />
                    <small v-if="serverErrors.precio_venta" class="text-red-500">{{ serverErrors.precio_venta[0] }}</small>
                </div>
            </div>
        </div>

        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" text @click="dialogVisible = false" />
            <Button label="Guardar" icon="pi pi-check" @click="updateProducto" :loading="loading" />
        </template>
    </Dialog>
</template>

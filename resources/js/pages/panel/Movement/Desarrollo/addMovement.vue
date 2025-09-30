<template>
    <Toolbar class="mb-6">
        <template #start>
            <Button label="Nuevo Movimiento" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openNew" />
        </template>
    </Toolbar>

    <Dialog v-model:visible="movementDialog" :style="{ width: '500px' }" header="Registro de Movimientos" :modal="true">
        <div class="flex flex-col gap-6">

                <!-- Tipo de Comprobante -->
                <div class="col-span-6">
                    <label class="block font-bold mb-2">
                        Tipo de Comprobante <span class="text-red-500">*</span>
                    </label>
                    <SelectButton 
                        v-model="movement.voucher_type" 
                        :options="voucherTypeOptions" 
                        optionLabel="label" 
                        optionValue="value"
                        :class="{ 'p-invalid': submitted && (!movement.voucher_type || serverErrors.voucher_type) }"
                    />
                    <small v-if="submitted && !movement.voucher_type" class="text-red-500">El tipo de comprobante es obligatorio.</small>
                    <small v-else-if="serverErrors.voucher_type" class="text-red-500">{{ serverErrors.voucher_type[0] }}</small>
                </div>
                <div class="col-span-6">
                    <label for="code" class="block font-bold mb-2">
                        Código <span class="text-red-500">*</span>
                    </label>
                    <InputText id="code" v-model.trim="movement.code" maxlength="255" fluid
                        :class="{ 'p-invalid': submitted && (!movement.code || serverErrors.code) }" />
                    <small v-if="submitted && !movement.code" class="text-red-500">El código es obligatorio.</small>
                    <small v-else-if="serverErrors.code" class="text-red-500">{{ serverErrors.code[0] }}</small>
                </div>

                <!-- Fecha -->
                <div class="col-span-6">
                    <label for="date" class="block font-bold mb-2">
                        Fecha de Emision <span class="text-red-500">*</span>
                    </label>
                    <DatePicker id="date" v-model="movement.date" dateFormat="yy-mm-dd" fluid
                        :class="{ 'p-invalid': submitted && (!movement.date || serverErrors.date) }" />
                    <small v-if="submitted && !movement.date" class="text-red-500">La fecha es obligatoria.</small>
                    <small v-else-if="serverErrors.date" class="text-red-500">{{ serverErrors.date[0] }}</small>
                </div>

                <!-- Proveedor -->
                <div class="col-span-12">
                    <label for="provider" class="block font-bold mb-2">
                        Proveedor <span class="text-red-500">*</span>
                    </label>
                    <AutoComplete 
                        id="provider"
                        v-model="selectedProvider" 
                        :suggestions="providerSuggestions" 
                        @complete="searchProviders"
                        @option-select="onProviderSelect"
                        optionLabel="razon_social"
                        placeholder="Buscar proveedor..."
                        fluid
                        :class="{ 'p-invalid': submitted && (!movement.provider_id || serverErrors.provider_id) }"
                    >
                        <template #option="slotProps">
                            <div class="flex align-items-center gap-2">
                                <div class="flex flex-col">
                                    <span class="font-semibold">{{ slotProps.option.razon_social }}</span>
                                    <small class="text-gray-500">RUC: {{ slotProps.option.ruc }}</small>
                                </div>
                            </div>
                        </template>
                    </AutoComplete>
                    <small v-if="submitted && !movement.provider_id" class="text-red-500">El proveedor es obligatorio.</small>
                    <small v-else-if="serverErrors.provider_id" class="text-red-500">{{ serverErrors.provider_id[0] }}</small>
                </div>

                <!-- Tipo de Pago -->
                <div class="col-span-12">
                    <label class="block font-bold mb-2">
                        Tipo de Pago <span class="text-red-500">*</span>
                    </label>
                    <SelectButton 
                        v-model="movement.payment_type" 
                        :options="paymentTypeOptions" 
                        optionLabel="label" 
                        optionValue="value"
                        :class="{ 'p-invalid': submitted && (!movement.payment_type || serverErrors.payment_type) }"
                    />
                    <small v-if="submitted && !movement.payment_type" class="text-red-500">El tipo de pago es obligatorio.</small>
                    <small v-else-if="serverErrors.payment_type" class="text-red-500">{{ serverErrors.payment_type[0] }}</small>
                </div>

                <!-- Fecha de Crédito (solo si es crédito) -->
                <div v-if="movement.payment_type === 'credito'" class="col-span-6">
                    <label for="credit_date" class="block font-bold mb-2">
                        Fecha de Crédito <span class="text-red-500">*</span>
                    </label>
                    <DatePicker id="credit_date" v-model="movement.credit_date" dateFormat="yy-mm-dd" fluid
                        :class="{ 'p-invalid': submitted && movement.payment_type === 'credito' && (!movement.credit_date || serverErrors.credit_date) }" />
                    <small v-if="submitted && movement.payment_type === 'credito' && !movement.credit_date" class="text-red-500">La fecha de crédito es obligatoria.</small>
                    <small v-else-if="serverErrors.credit_date" class="text-red-500">{{ serverErrors.credit_date[0] }}</small>
                </div>

                <!-- Incluye IGV -->
                <div class="col-span-6">
                    <label class="block font-bold mb-2">
                        Incluye IGV <span class="text-red-500">*</span>
                    </label>
                    <SelectButton 
                        v-model="movement.includes_igv" 
                        :options="igvOptions" 
                        optionLabel="label" 
                        optionValue="value"
                        :class="{ 'p-invalid': serverErrors.includes_igv }"
                    />
                    <small v-if="serverErrors.includes_igv" class="text-red-500">{{ serverErrors.includes_igv[0] }}</small>
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
                    <Button 
                        label="Guardar" 
                        icon="pi pi-check" 
                        :loading="loading" 
                        :disabled="!isFormValid || loading"
                        @click="guardarMovement" 
                    />
                </div>
            </div>
        </template>
    </Dialog>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import Toolbar from 'primevue/toolbar';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import DatePicker from 'primevue/datepicker';
import AutoComplete from 'primevue/autocomplete';
import SelectButton from 'primevue/selectbutton';
import { useToast } from 'primevue/usetoast';
import { defineEmits } from 'vue';

const emit = defineEmits(['agregado']);
const toast = useToast();

const movementDialog = ref(false);
const submitted = ref(false);
const loading = ref(false);
const serverErrors = ref({});

// Proveedor autocomplete
const selectedProvider = ref(null);
const providerSuggestions = ref([]);

const movement = ref({
    code: '',
    date: null,
    provider_id: '',
    payment_type: 'contado',
    credit_date: null,
    includes_igv: true,
    voucher_type: 'otros',
});

// Opciones para SelectButton
const paymentTypeOptions = [
    { label: 'Contado', value: 'contado' },
    { label: 'Crédito', value: 'credito' },
];

const igvOptions = [
    { label: 'Sí', value: true },
    { label: 'No', value: false },
];

const voucherTypeOptions = [
    { label: 'Factura', value: 'factura' },
    { label: 'Boleta', value: 'boleta' },
    { label: 'Otros', value: 'otros' },
];

// Computed para validar el formulario
const isFormValid = computed(() => {
    const basic = movement.value.code && 
                  movement.value.date && 
                  movement.value.provider_id && 
                  movement.value.payment_type &&
                  movement.value.voucher_type &&
                  (movement.value.includes_igv === true || movement.value.includes_igv === false);
    
    if (movement.value.payment_type === 'credito') {
        return basic && movement.value.credit_date;
    }
    
    return basic;
});

// Watch para limpiar fecha de crédito cuando cambia el tipo de pago
watch(() => movement.value.payment_type, (newValue) => {
    if (newValue !== 'credito') {
        movement.value.credit_date = null;
    }
});

// Watch para actualizar provider_id cuando se selecciona un proveedor
watch(selectedProvider, (newProvider) => {
    if (newProvider && typeof newProvider === 'object' && newProvider.id) {
        movement.value.provider_id = newProvider.id;
    } else if (typeof newProvider === 'string') {
        // Si es string, significa que el usuario escribió algo pero no seleccionó
        movement.value.provider_id = '';
    } else {
        movement.value.provider_id = '';
    }
});

// Función para manejar la selección del proveedor
function onProviderSelect(event) {
    selectedProvider.value = event.value;
    movement.value.provider_id = event.value.id;
}

// Buscar proveedores
async function searchProviders(event) {
    try {
        const response = await axios.get(`/providers?search=${event.query}`);
        let providers = response.data.data || [];
        
        // Los proveedores ya vienen con la estructura correcta del API
        providerSuggestions.value = providers;
    } catch (error) {
        console.error('Error buscando proveedores:', error);
        providerSuggestions.value = [];
    }
}

function resetMovement() {
    movement.value = {
        code: '',
        date: null,
        provider_id: '',
        payment_type: 'contado',
        credit_date: null,
        includes_igv: true,
        voucher_type: 'otros',
    };
    selectedProvider.value = null;
    serverErrors.value = {};
    submitted.value = false;
    loading.value = false;
}

function openNew() {
    resetMovement();
    movementDialog.value = true;
}

function hideDialog() {
    movementDialog.value = false;
    resetMovement();
}

// Función para formatear la fecha para el backend
function formatDateForBackend(date) {
    if (!date) return null;
    
    // Si es un objeto Date
    if (date instanceof Date) {
        return date.toISOString().split('T')[0];
    }
    
    // Si ya es una string en formato correcto
    if (typeof date === 'string') {
        return date;
    }
    
    return null;
}

async function guardarMovement() {
    submitted.value = true;
    serverErrors.value = {};
    loading.value = true;

    try {
        const payload = {
            ...movement.value,
            date: formatDateForBackend(movement.value.date),
            credit_date: formatDateForBackend(movement.value.credit_date),
        };

        const response = await axios.post('/movements', payload);
        
        toast.add({
            severity: 'success',
            summary: 'Éxito',
            detail: 'Movimiento registrado correctamente',
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
                detail: 'No se pudo registrar el movimiento',
                life: 3000,
            });
        }
    } finally {
        loading.value = false;
    }
}
</script>
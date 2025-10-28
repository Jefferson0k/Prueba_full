<template>
    <Dialog 
        v-model:visible="dialogVisible" 
        modal 
        :header="'Cobrar Tiempo Extra'" 
        :style="{ width: '35rem' }"
        :breakpoints="{ '960px': '75vw', '640px': '90vw' }"
    >
        <div class="flex flex-col gap-4">
            <div class="flex items-center gap-3 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                <i class="pi pi-exclamation-triangle text-red-500 text-2xl"></i>
                <p class="text-sm text-red-700 dark:text-red-300">
                    El tiempo de esta habitación ha vencido. Se cobrará el tiempo extra transcurrido.
                </p>
            </div>

            <div class="p-4 bg-surface-50 dark:bg-surface-800 rounded-lg">
                <div class="flex items-center gap-2 mb-3">
                    <i class="pi pi-home text-surface-600 dark:text-surface-400"></i>
                    <span class="font-semibold text-surface-700 dark:text-surface-300">
                        Habitación #{{ roomId }}
                    </span>
                </div>
                
                <div v-if="extraTimeData" class="space-y-3 mt-4">
                    <div class="flex justify-between items-center p-3 bg-white dark:bg-surface-900 rounded-lg">
                        <span class="text-sm text-surface-600 dark:text-surface-400">Tiempo extra:</span>
                        <span class="font-semibold text-surface-800 dark:text-surface-200">{{ extraTimeData.hours }} horas {{ extraTimeData.minutes }} minutos</span>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-white dark:bg-surface-900 rounded-lg">
                        <span class="text-sm text-surface-600 dark:text-surface-400">Tarifa por hora:</span>
                        <span class="font-semibold text-surface-800 dark:text-surface-200">S/ {{ extraTimeData.rate_per_hour }}</span>
                    </div>

                    <div class="flex justify-between items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border-2 border-green-400 dark:border-green-700">
                        <span class="font-semibold text-green-700 dark:text-green-300">Total a cobrar:</span>
                        <span class="font-bold text-2xl text-green-700 dark:text-green-300">S/ {{ extraTimeData.total_charge }}</span>
                    </div>
                </div>

                <div v-else class="flex justify-center py-4">
                    <i class="pi pi-spin pi-spinner text-3xl text-primary-500"></i>
                </div>
            </div>

            <div class="flex items-start gap-2 text-sm text-surface-600 dark:text-surface-400 p-3 bg-surface-100 dark:bg-surface-800 rounded-lg">
                <i class="pi pi-info-circle text-primary-500 mt-0.5"></i>
                <p>
                    Este cargo será agregado a la cuenta de la habitación. El tiempo comenzará a contar desde cero después del cobro.
                </p>
            </div>
        </div>

        <template #footer>
            <div class="flex justify-end gap-2">
                <Button 
                    label="Cancelar" 
                    severity="secondary"
                    outlined
                    @click="closeDialog"
                    :disabled="loading"
                />
                <Button 
                    label="Cobrar Tiempo Extra" 
                    severity="success"
                    icon="pi pi-dollar"
                    @click="cobrarTiempoExtra"
                    :loading="loading"
                    :disabled="!extraTimeData"
                />
            </div>
        </template>
    </Dialog>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';

const toast = useToast();

const props = defineProps({
    visible: {
        type: Boolean,
        default: false
    },
    roomId: {
        type: [Number, String],
        default: null
    }
});

const emit = defineEmits(['update:visible', 'extra-time-charged']);

const loading = ref(false);
const extraTimeData = ref(null);

const dialogVisible = computed({
    get: () => props.visible,
    set: (value) => emit('update:visible', value)
});

const closeDialog = () => {
    if (!loading.value) {
        dialogVisible.value = false;
        extraTimeData.value = null;
    }
};

const showSuccessToast = (message) => {
    toast.add({
        severity: 'success',
        summary: 'Éxito',
        detail: message,
        life: 3000
    });
};

const showErrorToast = (message) => {
    toast.add({
        severity: 'error',
        summary: 'Error',
        detail: message,
        life: 3000
    });
};

const fetchExtraTimeData = async () => {
    if (!props.roomId) return;

    try {
        const response = await axios.get(`/cuarto/${props.roomId}/calcular-tiempo-extra`);
        extraTimeData.value = response.data.data;
    } catch (error) {
        console.error('Error al calcular tiempo extra:', error);
        showErrorToast('Error al calcular el tiempo extra');
        closeDialog();
    }
};

const cobrarTiempoExtra = async () => {
    if (!props.roomId) {
        console.error('No se proporcionó un ID de habitación');
        return;
    }

    try {
        loading.value = true;
        
        const response = await axios.post(`/cuarto/${props.roomId}/cobrar-tiempo-extra`);

        showSuccessToast(response.data.message || 'Tiempo extra cobrado correctamente');
        emit('extra-time-charged');
        closeDialog();
        
    } catch (error) {
        console.error('Error al cobrar tiempo extra:', error);
        const errorMessage = error.response?.data?.message || 'Error al cobrar tiempo extra';
        showErrorToast(errorMessage);
    } finally {
        loading.value = false;
    }
};

watch(() => props.visible, (newValue) => {
    if (newValue && props.roomId) {
        fetchExtraTimeData();
    } else if (!newValue) {
        loading.value = false;
        extraTimeData.value = null;
    }
});
</script>
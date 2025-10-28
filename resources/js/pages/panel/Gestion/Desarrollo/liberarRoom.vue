<template>
    <Dialog 
        v-model:visible="dialogVisible" 
        modal 
        :header="'Liberar Habitación'" 
        :style="{ width: '30rem' }"
        :breakpoints="{ '960px': '75vw', '640px': '90vw' }"
    >
        <div class="flex flex-col gap-4">
            <div class="flex items-center gap-3 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <i class="pi pi-info-circle text-blue-500 text-2xl"></i>
                <p class="text-sm text-blue-700 dark:text-blue-300">
                    ¿Está seguro de que desea liberar esta habitación? La habitación pasará de estado de limpieza a disponible.
                </p>
            </div>

            <div class="p-4 bg-surface-50 dark:bg-surface-800 rounded-lg">
                <div class="flex items-center gap-2 mb-2">
                    <i class="pi pi-home text-surface-600 dark:text-surface-400"></i>
                    <span class="font-semibold text-surface-700 dark:text-surface-300">
                        Habitación #{{ roomId }}
                    </span>
                </div>
                <div class="flex items-center gap-2 text-sm text-surface-600 dark:text-surface-400">
                    <i class="pi pi-arrow-right text-xs"></i>
                    <span>Estado: Limpieza → Disponible</span>
                </div>
            </div>

            <div class="flex items-start gap-2 text-sm text-orange-700 dark:text-orange-400 p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-800">
                <i class="pi pi-exclamation-triangle text-orange-500 mt-0.5"></i>
                <p>
                    Esta acción indicará que la habitación ha sido limpiada y está lista para ser ocupada nuevamente.
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
                    label="Liberar Habitación" 
                    severity="success"
                    icon="pi pi-check-circle"
                    @click="liberarHabitacion"
                    :loading="loading"
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

const emit = defineEmits(['update:visible', 'room-liberated']);

const loading = ref(false);

const dialogVisible = computed({
    get: () => props.visible,
    set: (value) => emit('update:visible', value)
});

const closeDialog = () => {
    if (!loading.value) {
        dialogVisible.value = false;
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

const liberarHabitacion = async () => {
    if (!props.roomId) {
        console.error('No se proporcionó un ID de habitación');
        return;
    }

    try {
        loading.value = true;
        
        // Llamada con Axios al backend para liberar la habitación
        const response = await axios.post(`/cuarto/${props.roomId}/liberar`);

        // Mostrar toast de éxito
        showSuccessToast(response.data.message || 'Habitación liberada correctamente');
        
        emit('room-liberated');
        closeDialog();
        
    } catch (error) {
        console.error('Error al liberar la habitación:', error);
        
        // Mostrar toast de error con el mensaje del servidor
        const errorMessage = error.response?.data?.message || 'Error al liberar la habitación';
        showErrorToast(errorMessage);
        
    } finally {
        loading.value = false;
    }
};

// Resetear el loading cuando se cierra el diálogo
watch(() => props.visible, (newValue) => {
    if (!newValue) {
        loading.value = false;
    }
});
</script>
<template>
    <Dialog 
        v-model:visible="dialogVisible" 
        modal 
        :header="'Extender Tiempo'" 
        :style="{ width: '35rem' }"
        :breakpoints="{ '960px': '75vw', '640px': '90vw' }"
    >
        <div class="flex flex-col gap-4">
            <div class="flex items-center gap-3 p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-800">
                <i class="pi pi-clock text-orange-500 text-2xl"></i>
                <p class="text-sm text-orange-700 dark:text-orange-300">
                    El tiempo de esta habitación ha vencido. Puede extender el tiempo de estadía.
                </p>
            </div>

            <div class="p-4 bg-surface-50 dark:bg-surface-800 rounded-lg">
                <div class="flex items-center gap-2 mb-3">
                    <i class="pi pi-home text-surface-600 dark:text-surface-400"></i>
                    <span class="font-semibold text-surface-700 dark:text-surface-300">
                        Habitación #{{ roomId }}
                    </span>
                </div>
                
                <div class="flex flex-col gap-3 mt-4">
                    <label for="extra-hours" class="font-medium text-surface-700 dark:text-surface-300">
                        Horas adicionales
                    </label>
                    <div class="flex items-center gap-3">
                        <Button 
                            icon="pi pi-minus" 
                            severity="secondary"
                            outlined
                            @click="decrementHours"
                            :disabled="extraHours <= 1"
                        />
                        <InputNumber 
                            v-model="extraHours" 
                            inputId="extra-hours"
                            :min="1" 
                            :max="12"
                            showButtons
                            buttonLayout="horizontal"
                            class="flex-1"
                        >
                            <template #incrementbuttonicon>
                                <span class="pi pi-plus" />
                            </template>
                            <template #decrementbuttonicon>
                                <span class="pi pi-minus" />
                            </template>
                        </InputNumber>
                        <Button 
                            icon="pi pi-plus" 
                            severity="secondary"
                            outlined
                            @click="incrementHours"
                            :disabled="extraHours >= 12"
                        />
                    </div>
                </div>

                <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-blue-700 dark:text-blue-300">Tiempo adicional:</span>
                        <span class="font-bold text-lg text-blue-700 dark:text-blue-300">{{ extraHours }} {{ extraHours === 1 ? 'hora' : 'horas' }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-start gap-2 text-sm text-surface-600 dark:text-surface-400 p-3 bg-surface-100 dark:bg-surface-800 rounded-lg">
                <i class="pi pi-info-circle text-primary-500 mt-0.5"></i>
                <p>
                    El nuevo tiempo de checkout será calculado desde ahora más las horas adicionales seleccionadas.
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
                    label="Extender Tiempo" 
                    severity="warning"
                    icon="pi pi-clock"
                    @click="extenderTiempo"
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
import InputNumber from 'primevue/inputnumber';

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

const emit = defineEmits(['update:visible', 'time-extended']);

const loading = ref(false);
const extraHours = ref(1);

const dialogVisible = computed({
    get: () => props.visible,
    set: (value) => emit('update:visible', value)
});

const closeDialog = () => {
    if (!loading.value) {
        dialogVisible.value = false;
        extraHours.value = 1; // Reset
    }
};

const incrementHours = () => {
    if (extraHours.value < 12) {
        extraHours.value++;
    }
};

const decrementHours = () => {
    if (extraHours.value > 1) {
        extraHours.value--;
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

const extenderTiempo = async () => {
    if (!props.roomId) {
        console.error('No se proporcionó un ID de habitación');
        return;
    }

    try {
        loading.value = true;
        
        const response = await axios.post(`/cuarto/${props.roomId}/extender-tiempo`, {
            extra_hours: extraHours.value
        });

        showSuccessToast(response.data.message || 'Tiempo extendido correctamente');
        emit('time-extended');
        closeDialog();
        
    } catch (error) {
        console.error('Error al extender el tiempo:', error);
        const errorMessage = error.response?.data?.message || 'Error al extender el tiempo';
        showErrorToast(errorMessage);
    } finally {
        loading.value = false;
    }
};

watch(() => props.visible, (newValue) => {
    if (!newValue) {
        loading.value = false;
        extraHours.value = 1;
    }
});
</script>
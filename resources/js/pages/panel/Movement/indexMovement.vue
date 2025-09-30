<template>
    <Head title="Movimiento" />
    <AppLayout>
        <div>
            <template v-if="isLoading">
                <Espera />
            </template>
            <template v-else>
                <div class="card">
                    <addMovement @agregado="onMovementAdded" />
                    <listMovement 
                        ref="listMovementRef"
                        :refreshTrigger="refreshTrigger"
                        @movement-updated="onMovementUpdated"
                        @movement-deleted="onMovementDeleted"
                    />
                </div>
            </template>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import AppLayout from '@/layout/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import Espera from '@/components/Espera.vue';
import addMovement from './Desarrollo/addMovement.vue';
import listMovement from './Desarrollo/listMovement.vue';
import { useToast } from 'primevue/usetoast';

const toast = useToast();
const isLoading = ref(true);
const refreshTrigger = ref(0);
const listMovementRef = ref(null);

onMounted(() => {
    setTimeout(() => {
        isLoading.value = false;
    }, 1000);
});

// Cuando se agrega un nuevo movimiento
function onMovementAdded() {
    // Incrementar el trigger para refrescar la lista
    refreshTrigger.value++;
    
    toast.add({
        severity: 'success',
        summary: 'Actualizado',
        detail: 'La lista de movimientos se ha actualizado',
        life: 2000
    });
}

// Cuando se actualiza un movimiento
function onMovementUpdated() {
    refreshTrigger.value++;
}

// Cuando se elimina un movimiento
function onMovementDeleted() {
    // Ya se maneja en el componente hijo
}
</script>   
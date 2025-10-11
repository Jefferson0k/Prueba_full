<template>
    <div class="">
        <DataView :value="floors" :layout="layout">
            <template #header>
                <div class="flex justify-between items-center">
                    <div class="text-xl font-semibold">Gestión de Habitaciones</div>
                    <SelectButton v-model="layout" :options="options" :allowEmpty="false">
                        <template #option="{ option }">
                            <i :class="[option === 'list' ? 'pi pi-bars' : 'pi pi-table']" />
                        </template>
                    </SelectButton>
                </div>
            </template>

            <template #list="slotProps">
                <div class="flex flex-col gap-6">
                    <div v-for="(floor, floorIndex) in slotProps.items" :key="floor.id">
                        <div class="bg-primary-50 dark:bg-primary-900/20 p-4 rounded-t-lg border-b-2 border-primary-500">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="text-lg font-bold text-primary-700 dark:text-primary-300">
                                        {{ floor.name }}
                                    </h3>
                                    <p class="text-sm text-surface-600 dark:text-surface-400 mt-1">
                                        {{ floor.available_rooms }}/{{ floor.total_rooms }} habitaciones disponibles
                                    </p>
                                </div>
                                <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                    Piso {{ floor.floor_number }}
                                </div>
                            </div>
                        </div>

                        <div class="border border-t-0 border-surface-200 dark:border-surface-700 rounded-b-lg">
                            <div v-for="(room, roomIndex) in floor.rooms" :key="room.id">
                                <div 
                                    class="flex flex-col sm:flex-row sm:items-center p-6 gap-4 hover:bg-surface-50 dark:hover:bg-surface-800/50 transition-colors"
                                    :class="{ 'border-t border-surface-200 dark:border-surface-700': roomIndex !== 0 }"
                                >
                                    <div class="flex items-center justify-center w-20 h-20 bg-primary-100 dark:bg-primary-900/30 rounded-lg border-2 border-primary-300 dark:border-primary-700">
                                        <span class="text-2xl font-bold text-primary-700 dark:text-primary-300">
                                            {{ room.room_number }}
                                        </span>
                                    </div>

                                    <div class="flex flex-col md:flex-row justify-between md:items-center flex-1 gap-6">
                                        <div class="flex flex-col gap-3">
                                            <div>
                                                <span class="font-medium text-surface-500 dark:text-surface-400 text-sm">
                                                    Tipo de Habitación
                                                </span>
                                                <div class="text-lg font-semibold mt-1">{{ room.room_type }}</div>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <Tag 
                                                    :value="getStatusLabel(room.status)" 
                                                    :severity="getStatusSeverity(room.status)"
                                                />
                                                <Badge 
                                                    :value="room.is_active ? 'Activa' : 'Inactiva'" 
                                                    :severity="room.is_active ? 'success' : 'secondary'"
                                                />
                                            </div>
                                        </div>

                                        <div class="flex flex-col md:items-end gap-4">
                                            <div class="bg-surface-100 dark:bg-surface-700 px-4 py-2 rounded-lg">
                                                <div class="flex items-center gap-2">
                                                    <i class="pi pi-clock text-lg"></i>
                                                    <span class="font-mono text-lg font-semibold">00:00:00</span>
                                                </div>
                                            </div>
                                            
                                            <div class="flex gap-2">
                                                <Button 
                                                    icon="pi pi-eye" 
                                                    severity="info"
                                                    outlined
                                                    size="small"
                                                    @click="viewRoomDetails(room.id)"
                                                />
                                                <Button 
                                                    icon="pi pi-pencil" 
                                                    severity="warning"
                                                    outlined
                                                    size="small"
                                                />
                                                <Button 
                                                    icon="pi pi-cog" 
                                                    severity="secondary"
                                                    outlined
                                                    size="small"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <template #grid="slotProps">
                <div class="flex flex-col gap-6">
                    <div v-for="floor in slotProps.items" :key="floor.id">
                        <div class="bg-primary-50 dark:bg-primary-900/20 p-4 rounded-lg border border-primary-200 dark:border-primary-800">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="text-lg font-bold text-primary-700 dark:text-primary-300">
                                        {{ floor.name }}
                                    </h3>
                                    <p class="text-sm text-surface-600 dark:text-surface-400 mt-1">
                                        {{ floor.available_rooms }}/{{ floor.total_rooms }} disponibles
                                    </p>
                                </div>
                                <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                    Piso {{ floor.floor_number }}
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-12 gap-4">
                            <div 
                                v-for="room in floor.rooms" 
                                :key="room.id" 
                                class="col-span-12 sm:col-span-6 md:col-span-4 xl:col-span-3"
                            >
                                <div class="p-6 border border-surface-200 dark:border-surface-700 bg-surface-0 dark:bg-surface-900 rounded-lg hover:shadow-lg transition-shadow h-full flex flex-col">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex items-center justify-center w-16 h-16 bg-primary-100 dark:bg-primary-900/30 rounded-lg border-2 border-primary-300 dark:border-primary-700">
                                            <span class="text-xl font-bold text-primary-700 dark:text-primary-300">
                                                {{ room.room_number }}
                                            </span>
                                        </div>
                                        <Tag 
                                            :value="getStatusLabel(room.status)" 
                                            :severity="getStatusSeverity(room.status)"
                                        />
                                    </div>

                                    <div class="mb-4 flex-1">
                                        <span class="font-medium text-surface-500 dark:text-surface-400 text-sm">
                                            Tipo de Habitación
                                        </span>
                                        <div class="text-lg font-semibold mt-1">{{ room.room_type }}</div>
                                    </div>

                                    <div class="mb-4">
                                        <Badge 
                                            :value="room.is_active ? 'Activa' : 'Inactiva'" 
                                            :severity="room.is_active ? 'success' : 'secondary'"
                                        />
                                    </div>

                                    <div class="bg-surface-100 dark:bg-surface-700 px-4 py-3 rounded-lg mb-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <i class="pi pi-clock text-lg"></i>
                                            <span class="font-mono text-lg font-semibold">00:00:00</span>
                                        </div>
                                    </div>

                                    <div class="flex gap-2">
                                        <Button 
                                            icon="pi pi-eye" 
                                            severity="info"
                                            outlined
                                            class="flex-1"
                                            size="small"
                                            @click="viewRoomDetails(room.id)"
                                        />
                                        <Button 
                                            icon="pi pi-pencil" 
                                            severity="warning"
                                            outlined
                                            class="flex-1"
                                            size="small"
                                        />
                                        <Button 
                                            icon="pi pi-cog" 
                                            severity="secondary"
                                            outlined
                                            class="flex-1"
                                            size="small"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <template #empty>
                <div v-if="loading">
                    <div v-if="layout === 'list'" class="flex flex-col">
                        <div v-for="i in 6" :key="i">
                            <div class="flex flex-col xl:flex-row xl:items-start p-6 gap-6" :class="{ 'border-t border-surface-200 dark:border-surface-700': i !== 0 }">
                                <Skeleton class="w-9/12 sm:w-64 xl:w-40 h-24 mx-auto" />
                                <div class="flex flex-col sm:flex-row justify-between items-center xl:items-start flex-1 gap-6">
                                    <div class="flex flex-col items-center sm:items-start gap-4">
                                        <Skeleton width="8rem" height="2rem" />
                                        <Skeleton width="6rem" height="1rem" />
                                        <div class="flex items-center gap-4">
                                            <Skeleton width="6rem" height="1rem" />
                                            <Skeleton width="3rem" height="1rem" />
                                        </div>
                                    </div>
                                    <div class="flex sm:flex-col items-center sm:items-end gap-4 sm:gap-2">
                                        <Skeleton width="4rem" height="2rem" />
                                        <Skeleton size="3rem" shape="circle" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="grid grid-cols-12 gap-4">
                        <div v-for="i in 6" :key="i" class="col-span-12 sm:col-span-6 xl:col-span-4 p-2">
                            <div class="p-6 border border-surface-200 dark:border-surface-700 bg-surface-0 dark:bg-surface-900 rounded">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <Skeleton width="6rem" height="2rem" />
                                    <Skeleton width="3rem" height="1rem" />
                                </div>
                                <div class="flex flex-col items-center gap-4 py-8">
                                    <Skeleton width="75%" height="10rem" />
                                    <Skeleton width="8rem" height="2rem" />
                                    <Skeleton width="6rem" height="1rem" />
                                </div>
                                <div class="flex items-center justify-between">
                                    <Skeleton width="4rem" height="2rem" />
                                    <Skeleton width="6rem" height="1rem" shape="circle" size="3rem" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center p-6">
                    <i class="pi pi-inbox text-4xl text-surface-400 mb-3"></i>
                    <p class="text-surface-600 dark:text-surface-400">No hay habitaciones disponibles</p>
                </div>
            </template>
        </DataView>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { router } from '@inertiajs/vue3';
import DataView from 'primevue/dataview';
import SelectButton from 'primevue/selectbutton';
import Tag from 'primevue/tag';
import Badge from 'primevue/badge';
import Button from 'primevue/button';
import Skeleton from 'primevue/skeleton';

const floors = ref([]);
const layout = ref('grid');
const options = ref(['list', 'grid']);
const loading = ref(true);

onMounted(async () => {
    await fetchFloors();
});

const fetchFloors = async () => {
    try {
        loading.value = true;
        const response = await fetch('/floors-rooms');
        const result = await response.json();
        floors.value = result.data;
    } catch (error) {
        console.error('Error al cargar pisos y habitaciones:', error);
    } finally {
        loading.value = false;
    }
};

const getStatusLabel = (status) => {
    const labels = {
        'available': 'Disponible',
        'occupied': 'Ocupada',
        'maintenance': 'Mantenimiento',
        'cleaning': 'Limpieza'
    };
    return labels[status] || status;
};

const getStatusSeverity = (status) => {
    const severities = {
        'available': 'success',
        'occupied': 'danger',
        'maintenance': 'warn',
        'cleaning': 'info'
    };
    return severities[status] || null;
};

const viewRoomDetails = (roomId) => {
    console.log('Navegando a habitación:', roomId);
    router.visit(`/panel/cuarto/${roomId}`);
};
</script>
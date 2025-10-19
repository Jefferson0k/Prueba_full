<template>
    <Toolbar class="mb-6">
        <template #start>
            <DatePicker 
                v-model="dates" 
                selectionMode="range" 
                :manualInput="false" 
                class="w-96" 
                placeholder="Seleccione un rango de fechas"
                @update:modelValue="onFechasSeleccionadas" 
            />
        </template>
        <template #center>
            <Select 
                v-model="subBranchSeleccionada" 
                :options="subBranches" 
                class="w-96"
                optionLabel="name" 
                optionValue="id" 
                showClear 
                placeholder="Seleccionar sucursal..."
                @change="onSubBranchSeleccionada"
            >
                <template #option="{ option }">
                    <div>
                        <strong>{{ option.name }}</strong>
                        <div class="text-sm">Código: {{ option.code }}</div>
                    </div>
                </template>
            </Select>
        </template>
        <template #end>
            <Button label="Ir a Habitaciones" icon="pi pi-arrow-right" severity="contrast" @click="goToHabitaciones" />
        </template>
    </Toolbar>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import Toolbar from 'primevue/toolbar';
import Button from 'primevue/button';
import DatePicker from 'primevue/datepicker';
import Select from "primevue/select";
import axios from "axios";

// Variables para fechas y sucursales
const dates = ref();
const subBranchSeleccionada = ref(null);
const subBranches = ref([]);

const onSubBranchSeleccionada = () => {
    console.log("Sub-branch seleccionada ID:", subBranchSeleccionada.value);
    // Aquí puedes agregar lógica adicional cuando se selecciona una sucursal
};

const onFechasSeleccionadas = () => {
    console.log("Fechas seleccionadas:", dates.value);
    // Aquí puedes agregar lógica adicional cuando se seleccionan las fechas
};

const cargarSubBranches = async () => {
    try {
        const response = await axios.get("/sub-branches/search");
        subBranches.value = response.data.data;
        if (subBranches.value.length > 0) {
            subBranchSeleccionada.value = subBranches.value[0].id;
            console.log("Sucursal por defecto:", subBranches.value[0].name);
        }
    } catch (error) {
        console.error('Error al cargar sucursales:', error);
    }
};

const goToHabitaciones = () => {
    router.visit('/panel/aperturar');
};

onMounted(() => {
    cargarSubBranches();
});
</script>
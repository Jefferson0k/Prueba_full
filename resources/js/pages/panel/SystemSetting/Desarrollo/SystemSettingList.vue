<template>
    <div>
        <h2>Configuraciones del Sistema</h2>
        <DataTable :value="settings" responsiveLayout="scroll">
            <Column field="key" header="Clave"></Column>
            <Column field="value" header="Valor"></Column>
            <Column field="type" header="Tipo"></Column>
            <Column field="group" header="Grupo"></Column>
            <Column header="Acciones">
                <template #body="slotProps">
                    <Button 
                        label="Editar" 
                        icon="pi pi-pencil" 
                        class="p-button-sm p-button-warning"
                        @click="editSetting(slotProps.data)"
                    />
                </template>
            </Column>
        </DataTable>

        <SystemSettingEdit 
            v-if="editVisible" 
            :setting="selectedSetting" 
            @close="editVisible = false" 
            @updated="onUpdated"
        />
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import SystemSettingEdit from './SystemSettingEdit.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';

const props = defineProps({
    settings: Array
});

const editVisible = ref(false);
const selectedSetting = ref(null);

const editSetting = (setting) => {
    selectedSetting.value = setting;
    editVisible.value = true;
};

const onUpdated = () => {
    editVisible.value = false;
    emit('update-setting');
};
</script>

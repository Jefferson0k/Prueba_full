<template>
    <Dialog header="Editar Configuración" :visible="true" modal @hide="$emit('close')">
        <div class="p-fluid">
            <div class="field">
                <label>Clave</label>
                <InputText v-model="form.key" disabled />
            </div>
            <div class="field">
                <label>Valor</label>
                <InputText v-model="form.value" />
            </div>
            <div class="field">
                <label>Grupo</label>
                <InputText v-model="form.group" disabled />
            </div>
        </div>
        <template #footer>
            <Button label="Cancelar" class="p-button-text" @click="$emit('close')" />
            <Button label="Guardar" class="p-button-primary" @click="updateSetting" />
        </template>
    </Dialog>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';

const props = defineProps({
    setting: Object
});

const form = ref({ ...props.setting });

const updateSetting = async () => {
    try {
        await axios.put(`/system-settings/${form.value.id}`, form.value);
        emit('updated');
    } catch (error) {
        console.error(error);
    }
};
</script>

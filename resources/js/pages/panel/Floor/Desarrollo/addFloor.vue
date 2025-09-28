<template>
  <Toolbar class="mb-6">
    <template #start>
      <Button label="Nuevo Piso" icon="pi pi-plus" class="mr-2" severity="contrast"  @click="showDialog = true" />
    </template>
    <template #center>
      <h2 class="m-0 text-xl font-semibold">{{ subBranch.name }}</h2>
    </template>
    <template #end>
      <Button label="Volver" icon="pi pi-arrow-left" class="mr-2" severity="secondary" @click="goBack" />
    </template>
  </Toolbar>

  <Dialog 
    v-model:visible="showDialog" 
    modal 
    :style="{ width: '450px' }" 
    header="Agregar Nuevo Piso"
  >
    <form @submit.prevent="submitForm" class="space-y-4">
      <div>
        <label for="name" class="block text-sm font-medium mb-1">Nombre del Piso</label>
        <InputText 
          id="name"
          v-model="form.name" 
          class="w-full"
          :class="{ 'p-invalid': errors.name }"
          placeholder="Ej: Primer Piso, Planta Baja"
        />
        <small v-if="errors.name" class="p-error">{{ errors.name }}</small>
      </div>

      <div>
        <label for="floor_number" class="block text-sm font-medium mb-1">Número de Piso</label>
        <InputNumber 
          id="floor_number"
          v-model="form.floor_number" 
          class="w-full"
          :class="{ 'p-invalid': errors.floor_number }"
          :min="0"
          placeholder="0, 1, 2, 3..."
        />
        <small v-if="errors.floor_number" class="p-error">{{ errors.floor_number }}</small>
        <small class="text-gray-500">0 = Planta Baja, 1 = Primer Piso, etc.</small>
      </div>

      <div>
        <label for="description" class="block text-sm font-medium mb-1">Descripción (Opcional)</label>
        <Textarea 
          id="description"
          v-model="form.description" 
          rows="3" 
          class="w-full"
          placeholder="Descripción adicional del piso"
        />
      </div>

      <div class="flex items-center">
        <Checkbox 
          id="is_active" 
          v-model="form.is_active" 
          binary 
        />
        <label for="is_active" class="ml-2">Activo</label>
      </div>

      <div class="flex justify-end gap-2 pt-4">
        <Button 
          label="Cancelar" 
          severity="secondary" 
          @click="closeDialog" 
        />
        <Button 
          label="Guardar" 
          type="submit" 
          :loading="loading"
        />
      </div>
    </form>
  </Dialog>
</template>

<script setup>
import { ref, reactive } from 'vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3'; // Importar router
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Textarea from 'primevue/textarea';
import Checkbox from 'primevue/checkbox';
import { useToast } from 'primevue/usetoast';
import Toolbar from 'primevue/toolbar';

const props = defineProps({
  subBranch: { type: Object, required: true }
});

const emit = defineEmits(['agregado']);

const toast = useToast();
const showDialog = ref(false);
const loading = ref(false);

const form = reactive({
  name: '',
  floor_number: 0,
  description: '',
  is_active: true
});

const errors = ref({});

function resetForm() {
  form.name = '';
  form.floor_number = 0;
  form.description = '';
  form.is_active = true;
  errors.value = {};
}

function closeDialog() {
  showDialog.value = false;
  resetForm();
}

async function submitForm() {
  try {
    loading.value = true;
    errors.value = {};

    const payload = {
      sub_branch_id: props.subBranch.id,
      name: form.name,
      floor_number: form.floor_number,
      description: form.description,
      is_active: form.is_active
    };

    await axios.post('/floors', payload);

    toast.add({
      severity: 'success',
      summary: 'Éxito',
      detail: 'Piso creado correctamente',
      life: 3000
    });

    emit('agregado');
    closeDialog();

  } catch (error) {
    console.error('Error creando piso:', error);
    
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors;
    } else {
      toast.add({
        severity: 'error',
        summary: 'Error',
        detail: 'No se pudo crear el piso',
        life: 3000
      });
    }
  } finally {
    loading.value = false;
  }
}

const goBack = () => {
  // Corregir la sintaxis de la plantilla literal
  router.visit(`/panel/branches/${props.subBranch.branch.id}/sub-branches`);
};
</script>
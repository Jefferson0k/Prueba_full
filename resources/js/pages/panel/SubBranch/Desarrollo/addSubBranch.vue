<template>
  <Toolbar class="mb-6">
    <template #start>
      <Button label="Nuevo Sub Sucursal" icon="pi pi-plus" class="mr-2" severity="contrast" @click="openNew" />
    </template>
    <template #center>
      <h2 class="m-0 text-xl font-semibold">{{ branch.name }}</h2>
    </template>
    <template #end>
      <Button label="Volver" icon="pi pi-arrow-left" class="mr-2" severity="secondary" @click="goBack" />
    </template>
  </Toolbar>

  <Dialog v-model:visible="dialogVisible" :style="{ width: '500px' }" header="Nueva Sub Sucursal" :modal="true">
    <div class="flex flex-col gap-6">
      <div>
        <label for="name" class="block font-bold mb-3">Nombre <span class="text-red-500">*</span></label>
        <InputText id="name" v-model.trim="subBranch.name" required autofocus :invalid="submitted && !subBranch.name"
          fluid />
        <small v-if="submitted && !subBranch.name" class="text-red-500">El nombre es obligatorio.</small>
      </div>

      <div>
        <label for="address" class="block font-bold mb-3">Dirección <span class="text-red-500">*</span></label>
        <Textarea id="address" v-model="subBranch.address" required rows="3" cols="20"
          :invalid="submitted && !subBranch.address" fluid />
        <small v-if="submitted && !subBranch.address" class="text-red-500">La dirección es obligatoria.</small>
      </div>

      <div class="grid grid-cols-12 gap-4">
        <div class="col-span-6">
          <label for="phone" class="block font-bold mb-3">Teléfono <span class="text-red-500">*</span></label>
          <InputText id="phone" v-model="subBranch.phone" :invalid="submitted && !subBranch.phone" fluid />
          <small v-if="submitted && !subBranch.phone" class="text-red-500">El teléfono es obligatorio.</small>
        </div>
        <div class="col-span-6">

      <div>
        <label for="status" class="block font-bold mb-3">Estado <span class="text-red-500">*</span></label>
        <Select id="status" v-model="subBranch.is_active" :options="statuses" optionLabel="label" optionValue="value"
          placeholder="Selecciona un estado" :invalid="submitted && subBranch.is_active === null" fluid />
        <small v-if="submitted && subBranch.is_active === null" class="text-red-500">El estado es obligatorio.</small>
      </div>
        </div>
      </div>
    </div>

    <template #footer>
      <Button label="Cancelar" icon="pi pi-times" text severity="secondary" @click="hideDialog" />
      <Button label="Guardar" icon="pi pi-check" :loading="loading" severity="contrast" @click="saveSubBranch" />
    </template>
  </Dialog>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import Toolbar from 'primevue/toolbar';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Select from 'primevue/select';
import { router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import axios from 'axios';

const props = defineProps({
  branch: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['agregado']);

const toast = useToast();
const submitted = ref(false);
const dialogVisible = ref(false);
const loading = ref(false);

const subBranch = ref({
  name: '',
  address: '',
  phone: '',
  is_active: null
});

const statuses = [
  { label: 'Activo', value: true },
  { label: 'Inactivo', value: false }
];

const openNew = () => {
  subBranch.value = {
    name: '',
    address: '',
    phone: '',
    is_active: null
  };
  submitted.value = false;
  dialogVisible.value = true;
};

const hideDialog = () => {
  dialogVisible.value = false;
  submitted.value = false;
};

const saveSubBranch = async () => {
  submitted.value = true;

  // Validación rápida en frontend
  if (!subBranch.value.name?.trim() ||
    !subBranch.value.address?.trim() ||
    !subBranch.value.phone?.trim() ||
    subBranch.value.is_active === null) {
    return;
  }

  loading.value = true;

  try {
    const payload = {
      branch_id: props.branch.id,
      name: subBranch.value.name.trim(),
      address: subBranch.value.address.trim(),
      phone: subBranch.value.phone.trim(),
      is_active: subBranch.value.is_active
    };

    await axios.post('/sub-branches', payload);

    toast.add({
      severity: 'success',
      summary: 'Éxito',
      detail: 'Sub sucursal creada exitosamente',
      life: 3000
    });

    dialogVisible.value = false;
    subBranch.value = { name: '', address: '', phone: '', is_active: null };
    submitted.value = false;
    emit('agregado');

  } catch (error) {
    console.error('Error completo del servidor:', error.response?.data);
    console.error('Status:', error.response?.status);

    if (error.response?.status === 422 && error.response?.data?.errors) {
      // Mostrar validaciones del backend
      const errors = error.response.data.errors;

      Object.keys(errors).forEach((field) => {
        const fieldErrors = errors[field];
        if (Array.isArray(fieldErrors)) {
          fieldErrors.forEach((message) => {
            toast.add({
              severity: 'error',
              summary: `Error en ${field}`,
              detail: message,
              life: 5000
            });
          });
        } else {
          toast.add({
            severity: 'error',
            summary: `Error en ${field}`,
            detail: fieldErrors,
            life: 5000
          });
        }
      });
    } else {
      // Para debug: mostrar más información del error
      console.error('Error completo:', error.response?.data);

      toast.add({
        severity: 'error',
        summary: 'Error',
        detail: error.response?.data?.message || 'No se pudo crear la sub sucursal',
        life: 3000
      });
    }
  } finally {
    loading.value = false;
  }
};

const goBack = () => {
  router.visit('/panel/sucursales');
};
</script>
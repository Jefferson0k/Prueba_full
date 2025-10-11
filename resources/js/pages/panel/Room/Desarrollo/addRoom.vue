<template>
  <div class="flex justify-between items-center mb-4">
    <div>
      <h3 class="text-xl font-semibold">
        Habitaciones - {{ floor.name }}
      </h3>
      <p class="text-gray-600 text-sm">
        {{ floor.sub_branch.name }} - Piso {{ getFloorLabel(floor.floor_number) }}
      </p>
    </div>
    <Button label="Nueva Habitación" icon="pi pi-plus" @click="showDialog = true" severity="contrast"/>
  </div>

  <Dialog v-model:visible="showDialog" modal :style="{ width: '500px' }" header="Agregar Nueva Habitación">
    <form @submit.prevent="submitForm" class="space-y-4">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label for="room_number" class="block text-sm font-medium mb-1">Número de Habitación *</label>
          <InputText id="room_number" v-model="form.room_number" class="w-full"
            :class="{ 'p-invalid': errors.room_number }" placeholder="101, 102, A1..." />
          <small v-if="errors.room_number" class="p-error">{{ errors.room_number }}</small>
        </div>

        <div>
          <label for="room_type_id" class="block text-sm font-medium mb-1">Tipo de Habitación *</label>
          <Select id="room_type_id" v-model="form.room_type_id" :options="roomTypes" optionLabel="name" optionValue="id"
            placeholder="Seleccionar tipo" class="w-full" :class="{ 'p-invalid': errors.room_type_id }"
            :loading="loadingRoomTypes" />
          <small v-if="errors.room_type_id" class="p-error">{{ errors.room_type_id }}</small>
        </div>
      </div>

      <div>
        <label for="name" class="block text-sm font-medium mb-1">Nombre de la Habitación (Opcional)</label>
        <InputText id="name" v-model="form.name" class="w-full"
          placeholder="Suite Presidencial, Habitación Deluxe..." />
      </div>

      <div>
        <label for="description" class="block text-sm font-medium mb-1">Descripción (Opcional)</label>
        <Textarea id="description" v-model="form.description" rows="3" class="w-full"
          placeholder="Descripción adicional de la habitación" />
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label for="status" class="block text-sm font-medium mb-1">Estado Inicial</label>
          <Select id="status" v-model="form.status" :options="statusOptions" optionLabel="label" optionValue="value"
            class="w-full" />
        </div>

        <div class="flex items-center pt-6">
          <Checkbox id="is_active" v-model="form.is_active" binary />
          <label for="is_active" class="ml-2">Activo</label>
        </div>
      </div>
    </form>
    <template #footer>
      <Button label="Cancelar" icon="pi pi-times"  text severity="secondary" @click="closeDialog" :disabled="loading" />
      <Button label="Guardar" icon="pi pi-check" severity="contrast" @click="submitForm" :loading="loading" />
    </template>
  </Dialog>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Checkbox from 'primevue/checkbox';
import { useToast } from 'primevue/usetoast';
import Select from 'primevue/select';

const props = defineProps({
  floor: { type: Object, required: true }
});

const emit = defineEmits(['agregado']);

const toast = useToast();
const showDialog = ref(false);
const loading = ref(false);
const loadingRoomTypes = ref(false);
const roomTypes = ref([]);

const statusOptions = [
  { label: 'Disponible', value: 'available' },
  { label: 'Mantenimiento', value: 'maintenance' }
];

const form = reactive({
  room_number: '',
  room_type_id: null,
  name: '',
  description: '',
  status: 'available',
  is_active: true
});

const errors = ref({});

function getFloorLabel(floorNumber) {
  if (floorNumber === 0) return 'Planta Baja';
  if (floorNumber === 1) return 'Primer Piso';
  if (floorNumber === 2) return 'Segundo Piso';
  return `Piso ${floorNumber}`;
}

async function fetchRoomTypes() {
  try {
    loadingRoomTypes.value = true;
    const response = await axios.get('/room-types?is_active=true');
    roomTypes.value = response.data.data || response.data;
  } catch (error) {
    console.error('Error cargando tipos de habitación:', error);
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'No se pudieron cargar los tipos de habitación',
      life: 3000
    });
  } finally {
    loadingRoomTypes.value = false;
  }
}

function resetForm() {
  form.room_number = '';
  form.room_type_id = null;
  form.name = '';
  form.description = '';
  form.status = 'available';
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
      floor_id: props.floor.id,
      room_type_id: form.room_type_id,
      room_number: form.room_number,
      name: form.name || null,
      description: form.description || null,
      status: form.status,
      is_active: form.is_active
    };

    await axios.post('/rooms', payload);

    toast.add({
      severity: 'success',
      summary: 'Éxito',
      detail: 'Habitación creada correctamente',
      life: 3000
    });

    emit('agregado');
    closeDialog();

  } catch (error) {
    console.error('Error creando habitación:', error);

    if (error.response?.status === 422) {
      errors.value = error.response.data.errors;
    } else {
      toast.add({
        severity: 'error',
        summary: 'Error',
        detail: 'No se pudo crear la habitación',
        life: 3000
      });
    }
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  fetchRoomTypes();
});
</script>

<template>
  <Dialog :visible="visible" @update:visible="updateVisible" modal :style="{ width: '500px' }" header="Editar Habitación">
    <form @submit.prevent="submitForm" class="space-y-4">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label for="edit_room_number" class="block text-sm font-medium mb-1">Número de Habitación *</label>
          <InputText id="edit_room_number" v-model="form.room_number" class="w-full"
            :class="{ 'p-invalid': errors.room_number }" placeholder="101, 102, A1..." />
          <small v-if="errors.room_number" class="p-error">{{ errors.room_number }}</small>
        </div>

        <div>
          <label for="edit_room_type_id" class="block text-sm font-medium mb-1">Tipo de Habitación *</label>
          <Select id="edit_room_type_id" v-model="form.room_type_id" :options="roomTypes" optionLabel="name" 
            optionValue="id" placeholder="Seleccionar tipo" class="w-full" 
            :class="{ 'p-invalid': errors.room_type_id }" :loading="loadingRoomTypes" />
          <small v-if="errors.room_type_id" class="p-error">{{ errors.room_type_id }}</small>
        </div>
      </div>

      <div>
        <label for="edit_name" class="block text-sm font-medium mb-1">Nombre de la Habitación (Opcional)</label>
        <InputText id="edit_name" v-model="form.name" class="w-full"
          placeholder="Suite Presidencial, Habitación Deluxe..." />
      </div>

      <div>
        <label for="edit_description" class="block text-sm font-medium mb-1">Descripción (Opcional)</label>
        <Textarea id="edit_description" v-model="form.description" rows="3" class="w-full"
          placeholder="Descripción adicional de la habitación" />
      </div>

      <div class="flex items-center">
        <Checkbox id="edit_is_active" v-model="form.is_active" binary />
        <label for="edit_is_active" class="ml-2">Activo</label>
      </div>
    </form>
    <template #footer>
      <Button label="Cancelar" icon="pi pi-times" text severity="secondary" 
        @click="closeDialog" :disabled="loading" />
      <Button label="Actualizar" icon="pi pi-check" severity="contrast" 
        @click="submitForm" :loading="loading" />
    </template>
  </Dialog>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';
import axios from 'axios';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Checkbox from 'primevue/checkbox';
import Select from 'primevue/select';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
  visible: { type: Boolean, required: true },
  room: { type: Object, default: null }
});

const emit = defineEmits(['update:visible', 'actualizado']);

const toast = useToast();
const loading = ref(false);
const loadingRoomTypes = ref(false);
const roomTypes = ref([]);

const form = reactive({
  room_number: '',
  room_type_id: null,
  name: '',
  description: '',
  is_active: true
});

const errors = ref({});

// Función para actualizar la visibilidad
function updateVisible(value) {
  emit('update:visible', value);
}

watch(() => props.visible, async (newVal) => {
  if (newVal && props.room) {
    await fetchRoomTypes();
    loadFormData();
  }
});

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

function loadFormData() {
  if (!props.room) return;
  
  form.room_number = props.room.room_number;
  form.room_type_id = props.room.room_type?.id || null;
  form.name = props.room.name || '';
  form.description = props.room.description || '';
  form.is_active = props.room.is_active;
}

function resetForm() {
  form.room_number = '';
  form.room_type_id = null;
  form.name = '';
  form.description = '';
  form.is_active = true;
  errors.value = {};
}

function closeDialog() {
  emit('update:visible', false);
  resetForm();
}

async function submitForm() {
  if (!props.room) return;
  
  try {
    loading.value = true;
    errors.value = {};

    const payload = {
      room_type_id: form.room_type_id,
      room_number: form.room_number,
      name: form.name || null,
      description: form.description || null,
      is_active: form.is_active
    };

    await axios.put(`/rooms/${props.room.id}`, payload);

    toast.add({
      severity: 'success',
      summary: 'Éxito',
      detail: 'Habitación actualizada correctamente',
      life: 3000
    });

    emit('actualizado');
    closeDialog();

  } catch (error) {
    console.error('Error actualizando habitación:', error);

    if (error.response?.status === 422) {
      errors.value = error.response.data.errors;
    } else {
      toast.add({
        severity: 'error',
        summary: 'Error',
        detail: 'No se pudo actualizar la habitación',
        life: 3000
      });
    }
  } finally {
    loading.value = false;
  }
}
</script>
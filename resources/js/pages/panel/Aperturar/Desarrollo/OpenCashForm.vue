<template>
  <div>
    <div class="mb-4">
      <h2 class="text-3xl font-bold">Aperturar Caja</h2>
      <p class="mt-2">Selecciona una caja para iniciar tu turno</p>
    </div>

    <div class="grid">
      
      <div class="col-12 lg:col-4">
        <div class="">
          <div class="text-center">
            <Avatar icon="pi pi-user" size="xlarge" class="mb-3" />
            <p class="text-sm mb-2">Usuario Autenticado</p>
            <h3 class="text-xl font-bold mb-1">{{ authenticatedUser?.name }}</h3>
            <p class="text-sm text-gray-600">{{ authenticatedUser?.email }}</p>
          </div>
        </div>
      </div>

      <div class="col-12 lg:col-8">
        <div class="">
            
          <div class="field mb-4">
            <label for="cash-register" class="font-bold block mb-2">
              <i class="pi pi-calculator mr-2"></i>
              Cajas Disponibles
            </label>
            <Dropdown
              id="cash-register"
              v-model="selectedCashRegister"
              :options="availableCashRegisters"
              optionLabel="name"
              placeholder="Selecciona una caja para aperturar..."
              class="w-full"
              :class="{ 'p-invalid': errors.cash_register }"
              :loading="loadingCashRegisters"
            >
              <template #value="slotProps">
                <div v-if="slotProps.value" class="flex align-items-center">
                  <i class="pi pi-calculator mr-2"></i>
                  <span>{{ slotProps.value.name }}</span>
                  <Tag 
                    :value="slotProps.value.status" 
                    :severity="getStatusSeverity(slotProps.value.status)" 
                    class="ml-auto"
                  />
                </div>
                <span v-else>{{ slotProps.placeholder }}</span>
              </template>
              <template #option="slotProps">
                <div class="flex align-items-center justify-content-between w-full">
                  <div class="flex align-items-center">
                    <i class="pi pi-calculator mr-2"></i>
                    <span>{{ slotProps.option.name }}</span>
                  </div>
                  <Tag 
                    :value="slotProps.option.status" 
                    :severity="getStatusSeverity(slotProps.option.status)" 
                  />
                </div>
              </template>
            </Dropdown>
            <small v-if="errors.cash_register" class="p-error block mt-2">
              {{ errors.cash_register }}
            </small>
          </div>

          <Message v-if="selectedCashRegister" severity="info" :closable="false" class="mb-4">
            <div class="grid">
              <div class="col-6">
                <p class="font-bold mb-1">Sucursal</p>
                <p>{{ selectedCashRegister.sub_branch?.name || 'N/A' }}</p>
              </div>
              <div class="col-6">
                <p class="font-bold mb-1">Estado Actual</p>
                <Tag 
                  :value="selectedCashRegister.status.toUpperCase()" 
                  :severity="getStatusSeverity(selectedCashRegister.status)" 
                />
              </div>
            </div>
          </Message>

          <div v-if="selectedCashRegister" class="field mb-4">
            <label for="opening-amount" class="font-bold block mb-2">
              <i class="pi pi-money-bill mr-2"></i>
              Monto de Apertura
            </label>
            <InputNumber
              id="opening-amount"
              v-model="openingAmount"
              mode="currency"
              currency="PEN"
              locale="es-PE"
              placeholder="Ingrese el monto inicial de caja"
              class="w-full"
              :class="{ 'p-invalid': errors.opening_amount }"
              :min="0"
              :minFractionDigits="2"
              :maxFractionDigits="2"
            />
            <small v-if="errors.opening_amount" class="p-error block mt-2">
              {{ errors.opening_amount }}
            </small>
            <small v-else class="text-gray-600 block mt-2">
              Ingrese el monto con el que iniciará la caja
            </small>
          </div>

          <Button
            label="Aperturar Caja"
            icon="pi pi-lock-open"
            @click="openCashRegister"
            :loading="isOpening"
            :disabled="!selectedCashRegister || !openingAmount"
            severity="contrast"
            class="w-full"
          />

        </div>
      </div>

    </div>

    <Message severity="warn" :closable="false" class="mt-4">
      <template #icon>
        <i class="pi pi-info-circle text-2xl"></i>
      </template>
      <div>
        <p class="font-bold mb-2">Información Importante</p>
        <ul class="pl-4">
          <li>Solo puedes aperturar cajas en estado <strong>"cerrada"</strong></li>
          <li>Una vez aperturada, la caja quedará asignada a tu usuario</li>
          <li>Podrás registrar movimientos y transacciones en la caja</li>
        </ul>
      </div>
    </Message>

  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import Avatar from 'primevue/avatar';
import Dropdown from 'primevue/dropdown';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import InputNumber from 'primevue/inputnumber';
import Message from 'primevue/message';
import { useToast } from 'primevue/usetoast';

const toast = useToast();
const page = usePage();

interface User {
  id: string;
  name: string;
  email: string;
  sub_branch_id: string;
}

interface CashRegister {
  id: string;
  name: string;
  status: string;
  is_active: boolean;
  sub_branch?: {
    id: string;
    name: string;
  };
}

const loadingCashRegisters = ref(false);
const isOpening = ref(false);
const availableCashRegisters = ref<CashRegister[]>([]);
const selectedCashRegister = ref<CashRegister | null>(null);
const openingAmount = ref<number | null>(null);
const errors = ref<{ cash_register?: string; opening_amount?: string }>({});

const authenticatedUser = computed(() => page.props.auth?.user as User);

const getStatusSeverity = (status: string) => {
  switch (status) {
    case 'abierta':
      return 'success';
    case 'cerrada':
      return 'secondary';
    case 'bloqueada':
      return 'danger';
    default:
      return 'info';
  }
};

const loadCashRegisters = async () => {
  loadingCashRegisters.value = true;
  try {
    const response = await axios.get(route('cash.cash-registers.index'), {
      params: {
        status: 'cerrada',
        is_active: true,
        per_page: 100
      }
    });

    if (response.data.success) {
      availableCashRegisters.value = response.data.data;
    }
  } catch (error) {
    console.error('Error loading cash registers:', error);
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Error al cargar las cajas disponibles',
      life: 3000
    });
  } finally {
    loadingCashRegisters.value = false;
  }
};

const openCashRegister = async () => {
  errors.value = {};

  if (!selectedCashRegister.value) {
    errors.value.cash_register = 'Debes seleccionar una caja';
    return;
  }

  if (!openingAmount.value || openingAmount.value < 0) {
    errors.value.opening_amount = 'Debes ingresar un monto de apertura válido';
    return;
  }

  isOpening.value = true;

  try {
    const response = await axios.post(
      route('cash.cash-registers.open', selectedCashRegister.value.id),
      {
        opening_amount: openingAmount.value
      }
    );

    if (response.data.success) {
      toast.add({
        severity: 'success',
        summary: 'Éxito',
        detail: 'Caja aperturada correctamente',
        life: 3000
      });

      selectedCashRegister.value = null;
      openingAmount.value = null;
      await loadCashRegisters();
    }
  } catch (error: any) {
    console.error('Error opening cash register:', error);
    
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors;
    }
    
    const errorMessage = error.response?.data?.message || 'Error al aperturar la caja';
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: errorMessage,
      life: 3000
    });
  } finally {
    isOpening.value = false;
  }
};

onMounted(async () => {
  await loadCashRegisters();
});
</script>
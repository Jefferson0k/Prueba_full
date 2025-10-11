<template>
  <Dialog v-model:visible="visible" modal header="Agregar Producto" :style="{ width: '600px' }">
    <div class="flex flex-col gap-4">
      <!-- Buscador de productos -->
      <div class="flex flex-col gap-2">
        <label class="font-semibold text-sm">Buscar Producto <span class="text-red-500">*</span></label>
        
        <Select
          v-model="productoSeleccionado"
          :options="productos"
          :style="{ width: '100%' }"
          editable
          optionLabel="label"
          optionValue="value"
          showClear
          placeholder="Buscar por nombre o código..."
          @input="onInputChange"
          @change="onProductoSeleccionado"
        >
          <template #option="{ option }">
            <div>
              <strong>{{ option.label }}</strong>
              <div class="text-sm">{{ option.sublabel }}</div>
            </div>
          </template>
          <template #empty>Producto no encontrado.</template>
        </Select>
      </div>

      <!-- Tipo de cantidad -->
      <div v-if="productoCompleto" class="flex flex-col gap-2">
        <label class="font-semibold text-sm">Tipo de Cantidad <span class="text-red-500">*</span></label>
        
        <Select
          v-model="tipoSeleccionado"
          :options="opcionesTipo"
          :style="{ width: '100%' }"
          optionLabel="label"
          optionValue="value"
          placeholder="Seleccione tipo..."
        />
      </div>

      <!-- Cantidad según tipo seleccionado -->
      <div v-if="tipoSeleccionado" class="flex flex-col gap-2">
        <!-- Solo Paquetes -->
        <div v-if="tipoSeleccionado === 'paquete'" class="flex flex-col gap-2">
          <label class="font-semibold text-sm">Cantidad de Paquetes <span class="text-red-500">*</span></label>
          <InputNumber
            v-model="cantidadPaquetes"
            :min="1"
            :style="{ width: '100%' }"
            placeholder="Ingrese cantidad de paquetes"
          />
          <small v-if="productoCompleto.is_fractionable" class="text-gray-500">
            Total de fracciones: {{ cantidadPaquetes * productoCompleto.fraction_units }}
          </small>
        </div>

        <!-- Solo Fracciones -->
        <div v-if="tipoSeleccionado === 'fraccion'" class="flex flex-col gap-2">
          <label class="font-semibold text-sm">Cantidad de Fracciones <span class="text-red-500">*</span></label>
          <InputNumber
            v-model="cantidadFracciones"
            :min="1"
            :style="{ width: '100%' }"
            placeholder="Ingrese cantidad de fracciones"
          />
          <small v-if="productoCompleto.is_fractionable && cantidadFracciones" class="text-gray-500">
            Equivale a: {{ Math.floor(cantidadFracciones / productoCompleto.fraction_units) }} paquete(s) y {{ cantidadFracciones % productoCompleto.fraction_units }} fracción(es)
          </small>
        </div>

        <!-- Ambas -->
        <div v-if="tipoSeleccionado === 'ambas'" class="flex flex-col gap-4">
          <div class="flex gap-4">
            <div class="flex flex-col gap-2 flex-1">
              <label class="font-semibold text-sm">Paquetes</label>
              <InputNumber
                v-model="cantidadPaquetes"
                :min="0"
                :style="{ width: '100%' }"
                placeholder="Paquetes"
              />
            </div>
            <div class="flex flex-col gap-2 flex-1">
              <label class="font-semibold text-sm">Fracciones</label>
              <InputNumber
                v-model="cantidadFracciones"
                :min="0"
                :style="{ width: '100%' }"
                placeholder="Fracciones"
                @update:modelValue="onFraccionesChange"
              />
            </div>
          </div>
          <small v-if="productoCompleto.is_fractionable && (cantidadPaquetes || cantidadFracciones)" class="text-gray-500">
            Total: {{ calcularTotalPaquetes }} paquete(s) y {{ calcularTotalFracciones }} fracción(es) = {{ calcularTotalFraccionesAbsolutas }} fracciones totales
          </small>
        </div>
      </div>

      <!-- Precio Total -->
      <div v-if="tipoSeleccionado" class="flex flex-col gap-2">
        <label class="font-semibold text-sm">Precio Total <span class="text-red-500">*</span></label>
        <InputNumber
          v-model="precioTotal"
          :min="0"
          :minFractionDigits="2"
          :maxFractionDigits="2"
          :style="{ width: '100%' }"
          placeholder="0.00"
          mode="currency"
          currency="PEN"
          locale="es-PE"
        />
      </div>

      <!-- Precio Unitario (calculado) -->
      <div v-if="tipoSeleccionado && precioTotal" class="flex flex-col gap-2">
        <div class="flex items-center justify-between">
          <label class="font-semibold text-sm">Precio Unitario (por fracción)</label>
          <div class="flex items-center gap-2">
            <Checkbox v-model="editarPrecioManual" inputId="editarManual" binary />
            <label for="editarManual" class="text-sm cursor-pointer">Editar manualmente</label>
          </div>
        </div>
        <InputNumber
          v-model="precioUnitario"
          :min="0"
          :minFractionDigits="2"
          :maxFractionDigits="2"
          :style="{ width: '100%' }"
          :disabled="!editarPrecioManual"
          placeholder="0.00"
          mode="currency"
          currency="PEN"
          locale="es-PE"
        />
        <small class="text-gray-500">
          {{ editarPrecioManual ? '⚠️ Editando manualmente' : `Calculado: S/ ${precioTotal} ÷ ${totalUnidades} unidades` }}
        </small>
      </div>

      <!-- Fecha de Vencimiento -->
      <div v-if="tipoSeleccionado" class="flex flex-col gap-2">
        <label class="font-semibold text-sm">Fecha de Vencimiento</label>
        <DatePicker
          v-model="fechaVencimiento"
          :style="{ width: '100%' }"
          dateFormat="dd/mm/yy"
          placeholder="Seleccione fecha"
          showIcon
          :minDate="new Date()"
        />
      </div>
    </div>

    <template #footer>
      <Button 
        label="Cancelar" 
        icon="pi pi-times" 
        @click="closeDialog" 
        severity="secondary"
        :disabled="guardando"
      />
      <Button
        label="Guardar"
        icon="pi pi-check"
        @click="saveProduct"
        severity="contrast"
        :disabled="!formularioValido || guardando"
        :loading="guardando"
      />
    </template>
  </Dialog>
</template>

<script setup lang="ts">
import { ref, watch, computed, onUnmounted } from 'vue';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import Select from 'primevue/select';
import InputNumber from 'primevue/inputnumber';
import DatePicker from 'primevue/datepicker';
import Checkbox from 'primevue/checkbox';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';

const toast = useToast();

interface Product {
  id: string;
  codigo: string;
  nombre: string;
  descripcion: string;
  precio_compra: string;
  precio_venta: string;
  unidad: string;
  categoria_id: string;
  Categoria_nombre: string;
  estado: boolean;
  is_fractionable: boolean;
  fraction_units: number;
  creacion: string;
  actualizacion: string;
}

const props = defineProps<{
  movementId: string;
}>();

const visible = defineModel<boolean>('visible');
const emit = defineEmits(['product-added']);

const productoSeleccionado = ref(null);
const productos = ref([]);
const productoCompleto = ref<Product | null>(null);
const timeoutRef = ref<NodeJS.Timeout | null>(null);

const tipoSeleccionado = ref<string | null>(null);
const cantidadPaquetes = ref<number>(0);
const cantidadFracciones = ref<number>(0);
const precioTotal = ref<number>(0);
const precioUnitario = ref<number>(0);
const fechaVencimiento = ref<Date | null>(null);
const editarPrecioManual = ref<boolean>(false);
const guardando = ref<boolean>(false);

// Auto-ajustar cuando las fracciones exceden el paquete
const onFraccionesChange = () => {
  if (!productoCompleto.value || !productoCompleto.value.is_fractionable) return;
  
  if (cantidadFracciones.value >= productoCompleto.value.fraction_units) {
    const paquetesExtra = Math.floor(cantidadFracciones.value / productoCompleto.value.fraction_units);
    const fraccionesRestantes = cantidadFracciones.value % productoCompleto.value.fraction_units;
    
    cantidadPaquetes.value = (cantidadPaquetes.value || 0) + paquetesExtra;
    cantidadFracciones.value = fraccionesRestantes;
  }
};

// Opciones de tipo de cantidad
const opcionesTipo = computed(() => {
  if (!productoCompleto.value) return [];
  
  if (productoCompleto.value.is_fractionable) {
    return [
      { label: 'Paquete', value: 'paquete' },
      { label: 'Fracción', value: 'fraccion' },
      { label: 'Ambas', value: 'ambas' }
    ];
  } else {
    return [
      { label: 'Unidades', value: 'paquete' }
    ];
  }
});

// Calcular totales para modo "Ambas"
const calcularTotalPaquetes = computed(() => {
  if (!productoCompleto.value || tipoSeleccionado.value !== 'ambas') return 0;
  
  const totalFracciones = (cantidadPaquetes.value || 0) * productoCompleto.value.fraction_units + (cantidadFracciones.value || 0);
  return Math.floor(totalFracciones / productoCompleto.value.fraction_units);
});

const calcularTotalFracciones = computed(() => {
  if (!productoCompleto.value || tipoSeleccionado.value !== 'ambas') return 0;
  
  const totalFracciones = (cantidadPaquetes.value || 0) * productoCompleto.value.fraction_units + (cantidadFracciones.value || 0);
  return totalFracciones % productoCompleto.value.fraction_units;
});

const calcularTotalFraccionesAbsolutas = computed(() => {
  if (!productoCompleto.value || tipoSeleccionado.value !== 'ambas') return 0;
  
  return (cantidadPaquetes.value || 0) * productoCompleto.value.fraction_units + (cantidadFracciones.value || 0);
});

// Calcular total de unidades
const totalUnidades = computed(() => {
  if (!productoCompleto.value) return 0;
  
  if (tipoSeleccionado.value === 'paquete') {
    return productoCompleto.value.is_fractionable 
      ? cantidadPaquetes.value * productoCompleto.value.fraction_units 
      : cantidadPaquetes.value;
  } else if (tipoSeleccionado.value === 'fraccion') {
    return cantidadFracciones.value;
  } else if (tipoSeleccionado.value === 'ambas') {
    return (cantidadPaquetes.value || 0) * productoCompleto.value.fraction_units + (cantidadFracciones.value || 0);
  }
  
  return 0;
});

// Calcular precio unitario automáticamente
watch([precioTotal, totalUnidades, editarPrecioManual], () => {
  if (!editarPrecioManual.value && totalUnidades.value > 0 && precioTotal.value > 0) {
    precioUnitario.value = parseFloat((precioTotal.value / totalUnidades.value).toFixed(2));
  }
});

// Validar que la cantidad sea válida
const validarCantidad = computed(() => {
  if (!tipoSeleccionado.value) return false;
  
  if (tipoSeleccionado.value === 'paquete') {
    return cantidadPaquetes.value > 0;
  } else if (tipoSeleccionado.value === 'fraccion') {
    return cantidadFracciones.value > 0;
  } else if (tipoSeleccionado.value === 'ambas') {
    return cantidadPaquetes.value > 0 || cantidadFracciones.value > 0;
  }
  
  return false;
});

// Validar formulario completo
const formularioValido = computed(() => {
  return productoCompleto.value && 
         tipoSeleccionado.value && 
         validarCantidad.value && 
         precioTotal.value > 0 &&
         props.movementId;
});

// Función debounce para buscar productos
const onInputChange = (evento) => {
  const textoIngresado = evento?.target?.value?.trim() || "";
  
  if (timeoutRef.value) {
    clearTimeout(timeoutRef.value);
  }
  
  if (!textoIngresado) {
    productos.value = [];
    return;
  }
  
  timeoutRef.value = setTimeout(() => {
    buscarProductos(textoIngresado);
  }, 500);
};

const buscarProductos = async (textoIngresado: string) => {
  if (!textoIngresado) {
    productos.value = [];
    return;
  }

  try {
    const response = await axios.get("/producto", {
      params: {
        search: textoIngresado,
        per_page: 20
      },
    });

    productos.value = response.data.data.map((producto) => ({
      label: `${producto.nombre}`,
      sublabel: `Código: ${producto.codigo} - ${producto.Categoria_nombre}`,
      value: producto.id,
      data: producto
    }));
  } catch (error) {
    console.error('Error al buscar productos:', error);
    toast.add({
      severity: "error",
      summary: "Error",
      detail: "Error al buscar productos",
      life: 3000,
    });
  }
};

const onProductoSeleccionado = () => {
  if (productoSeleccionado.value) {
    const productoEncontrado = productos.value.find(p => p.value === productoSeleccionado.value);
    if (productoEncontrado) {
      productoCompleto.value = productoEncontrado.data;
      tipoSeleccionado.value = null;
      cantidadPaquetes.value = 0;
      cantidadFracciones.value = 0;
      precioTotal.value = 0;
      precioUnitario.value = 0;
      fechaVencimiento.value = null;
      editarPrecioManual.value = false;
    }
  } else {
    productoCompleto.value = null;
  }
};

watch(productoSeleccionado, (newValue) => {
  if (newValue === null || newValue === '') {
    productoCompleto.value = null;
  }
});

onUnmounted(() => {
  if (timeoutRef.value) {
    clearTimeout(timeoutRef.value);
  }
});

const closeDialog = () => {
  visible.value = false;
  resetForm();
};

const resetForm = () => {
  productoSeleccionado.value = null;
  productos.value = [];
  productoCompleto.value = null;
  tipoSeleccionado.value = null;
  cantidadPaquetes.value = 0;
  cantidadFracciones.value = 0;
  precioTotal.value = 0;
  precioUnitario.value = 0;
  fechaVencimiento.value = null;
  editarPrecioManual.value = false;
  guardando.value = false;
  
  if (timeoutRef.value) {
    clearTimeout(timeoutRef.value);
    timeoutRef.value = null;
  }
};

const saveProduct = async () => {
  if (!productoCompleto.value || !tipoSeleccionado.value || !precioTotal.value || !props.movementId) {
    toast.add({
      severity: "warn",
      summary: "Advertencia",
      detail: "Complete todos los campos requeridos",
      life: 3000,
    });
    return;
  }

  guardando.value = true;

  try {
    // Calcular boxes y units_per_box según el tipo seleccionado
    let boxes = 0;
    let unitsPerBox = productoCompleto.value.fraction_units;

    if (tipoSeleccionado.value === 'paquete') {
      boxes = cantidadPaquetes.value;
    } else if (tipoSeleccionado.value === 'fraccion') {
      // Convertir fracciones a boxes
      boxes = Math.floor(cantidadFracciones.value / productoCompleto.value.fraction_units);
      // Si hay fracciones sobrantes, agregar una caja parcial
      if (cantidadFracciones.value % productoCompleto.value.fraction_units > 0) {
        boxes += 1;
      }
    } else if (tipoSeleccionado.value === 'ambas') {
      const totalFraccionesAbsolutas = (cantidadPaquetes.value || 0) * productoCompleto.value.fraction_units + (cantidadFracciones.value || 0);
      boxes = Math.floor(totalFraccionesAbsolutas / productoCompleto.value.fraction_units);
      if (totalFraccionesAbsolutas % productoCompleto.value.fraction_units > 0) {
        boxes += 1;
      }
    }

    // Preparar datos para enviar al backend
    const payload = {
      movement_id: props.movementId,
      product_id: productoCompleto.value.id,
      unit_price: precioUnitario.value,
      boxes: boxes,
      units_per_box: unitsPerBox,
      expiry_date: fechaVencimiento.value 
        ? new Date(fechaVencimiento.value).toISOString().split('T')[0] 
        : null
    };

    console.log('Enviando al backend:', payload);

    // Enviar petición POST al backend
    const response = await axios.post('/movement-detail', payload);

    console.log('Respuesta del servidor:', response.data);

    toast.add({
      severity: "success",
      summary: "Éxito",
      detail: response.data.message || "Producto agregado correctamente",
      life: 3000,
    });

    // Emitir evento para notificar al componente padre
    emit('product-added', response.data.data);

    closeDialog();

  } catch (error) {
    console.error('Error al guardar el producto:', error);
    
    let errorMessage = "Error al guardar el producto";
    
    if (error.response?.data?.message) {
      errorMessage = error.response.data.message;
    } else if (error.response?.data?.errors) {
      // Mostrar errores de validación
      const errors = Object.values(error.response.data.errors).flat();
      errorMessage = errors.join(', ');
    }

    toast.add({
      severity: "error",
      summary: "Error",
      detail: errorMessage,
      life: 5000,
    });
  } finally {
    guardando.value = false;
  }
};
</script>
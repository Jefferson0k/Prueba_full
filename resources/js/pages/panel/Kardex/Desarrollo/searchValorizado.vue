<template>
  <Toolbar class="mb-6">
    <template #start>
      <Select v-model="productoSeleccionado" :options="productos" editable class="w-96"
        optionLabel="label" optionValue="value" showClear placeholder="Buscar producto por nombre o código..."
        @input="onInputChange" @change="onProductoSeleccionado">
        <template #option="{ option }">
          <div>
            <strong>{{ option.label }}</strong>
            <div class="text-sm">{{ option.sublabel }}</div>
          </div>
        </template>
        <template #empty>Producto no encontrado.</template>
      </Select>
    </template>
    
    <template #center>
      <DatePicker v-model="dates" selectionMode="range" :manualInput="false" class="w-96" 
        placeholder="Seleccione fecha de inicio y fin" @update:modelValue="onFechasSeleccionadas" />
    </template>
    
    <template #end>
      <div class="flex gap-2">
        <Select v-model="subBranchSeleccionada" :options="subBranches" class="w-64"
          optionLabel="name" optionValue="id" showClear placeholder="Seleccionar sucursal"
          @change="onSubBranchSeleccionada">
          <template #option="{ option }">
            <div>
              <strong>{{ option.name }}</strong>
              <div class="text-sm">Código: {{ option.code }}</div>
            </div>
          </template>
        </Select>
        
        <Select v-model="movementTypeSeleccionado" :options="movementTypes" class="w-48"
          optionLabel="label" optionValue="value" showClear placeholder="Tipo movimiento"
          @change="onFiltroChange">
        </Select>
      </div>
    </template>
  </Toolbar>
  
  <!-- Mensaje de validación -->
  <Message v-if="showValidationMessage" severity="warn" :closable="false" class="mb-4">
    <div class="flex items-center gap-2">
      <i class="pi pi-exclamation-triangle text-xl"></i>
      <div>
        <strong>Filtros requeridos:</strong> Debe seleccionar un producto, rango de fechas y sucursal para buscar el kardex valorizado.
      </div>
    </div>
  </Message>
</template>

<script setup lang="ts">
import { ref, onUnmounted, onMounted, computed } from "vue";
import DatePicker from 'primevue/datepicker';
import Toolbar from 'primevue/toolbar';
import Select from "primevue/select";
import Message from 'primevue/message';
import axios from "axios";
import { useKardexValorizadoStore } from './kardexValorizadoStore';

const kardexValorizadoStore = useKardexValorizadoStore();

const dates = ref();
const productoSeleccionado = ref(null);
const productos = ref([]);
const timeoutRef = ref(null);

// Variables para sub-branches
const subBranchSeleccionada = ref(null);
const subBranches = ref([]);

// Variable para tipo de movimiento
const movementTypeSeleccionado = ref(null);
const movementTypes = ref([
  { label: 'Entrada', value: 'entrada' },
  { label: 'Salida', value: 'salida' }
]);

// Computed para mostrar mensaje de validación
const showValidationMessage = computed(() => {
  return !productoSeleccionado.value || !dates.value || !dates.value[0] || !dates.value[1] || !subBranchSeleccionada.value;
});

const onProductoSeleccionado = () => {
  console.log("Producto seleccionado ID:", productoSeleccionado.value);
  buscarKardexValorizado();
};

const onSubBranchSeleccionada = () => {
  console.log("Sub-branch seleccionada ID:", subBranchSeleccionada.value);
  buscarKardexValorizado();
};

const onFechasSeleccionadas = () => {
  console.log("Fechas seleccionadas:", dates.value);
  if (dates.value && dates.value.length === 2 && dates.value[0] && dates.value[1]) {
    buscarKardexValorizado();
  }
};

const onFiltroChange = () => {
  buscarKardexValorizado();
};

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
  }
};

// Función para buscar kardex valorizado
const buscarKardexValorizado = async () => {
  // Verificar que todos los parámetros OBLIGATORIOS estén presentes
  if (!productoSeleccionado.value || !dates.value || !dates.value[0] || !dates.value[1] || !subBranchSeleccionada.value) {
    console.log("Faltan parámetros obligatorios para realizar la búsqueda");
    // Limpiar datos si faltan parámetros
    kardexValorizadoStore.clearKardexValorizado();
    return;
  }

  // Formatear fechas
  const formatDate = (date) => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  };

  const fechaInicio = formatDate(dates.value[0]);
  const fechaFin = formatDate(dates.value[1]);

  const params: any = {
    product_id: productoSeleccionado.value,
    fecha_inicio: fechaInicio,
    fecha_fin: fechaFin,
    sub_branch_id: subBranchSeleccionada.value
  };

  // Agregar tipo de movimiento si está seleccionado (opcional)
  if (movementTypeSeleccionado.value) {
    params.movement_type = movementTypeSeleccionado.value;
  }

  console.log("Buscando kardex valorizado con parámetros:", params);
  
  // Cargar los datos en el store
  await kardexValorizadoStore.fetchKardexValorizado(params);
};

// Función para cargar las sub-branches
const cargarSubBranches = async () => {
  try {
    const response = await axios.get("/sub-branches/search");
    subBranches.value = response.data.data;
    
    // Seleccionar automáticamente la primera (que será la del usuario autenticado)
    if (subBranches.value.length > 0) {
      subBranchSeleccionada.value = subBranches.value[0].id;
      console.log("Sub-branch por defecto:", subBranches.value[0].name);
    }
  } catch (error) {
    console.error('Error al cargar sub-branches:', error);
  }
};

onMounted(() => {
  cargarSubBranches();
});

onUnmounted(() => {
  if (timeoutRef.value) {
    clearTimeout(timeoutRef.value);
  }
});
</script>
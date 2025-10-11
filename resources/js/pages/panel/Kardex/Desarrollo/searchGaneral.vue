<template>
  <Toolbar class="mb-6">
    <template #start>
      <div class="flex gap-2">
        <Select v-model="movementTypeSeleccionado" :options="movementTypes" class="w-48"
          optionLabel="label" optionValue="value" showClear placeholder="Tipo de movimiento"
          @change="onFiltroChange">
        </Select>
        
        <Select v-model="movementCategorySeleccionado" :options="movementCategories" class="w-48"
          optionLabel="label" optionValue="value" showClear placeholder="Categoría"
          @change="onFiltroChange">
        </Select>
      </div>
    </template>
    
    <template #center>
      <DatePicker v-model="dates" selectionMode="range" :manualInput="false" class="w-96" 
        placeholder="Seleccione rango de fechas" @update:modelValue="onFechasSeleccionadas" />
    </template>
    
    <template #end>
      <div class="flex gap-2">
        <Select v-model="productoSeleccionado" :options="productos" editable class="w-64"
          optionLabel="label" optionValue="value" showClear placeholder="Buscar producto..."
          @input="onInputChange" @change="onFiltroChange">
          <template #option="{ option }">
            <div>
              <strong>{{ option.label }}</strong>
              <div class="text-sm">{{ option.sublabel }}</div>
            </div>
          </template>
          <template #empty>Producto no encontrado.</template>
        </Select>
        
        <Select v-model="subBranchSeleccionada" :options="subBranches" class="w-64"
          optionLabel="name" optionValue="id" showClear placeholder="Seleccionar sucursal"
          @change="onFiltroChange">
          <template #option="{ option }">
            <div>
              <strong>{{ option.name }}</strong>
              <div class="text-sm">Código: {{ option.code }}</div>
            </div>
          </template>
        </Select>
        
        <Button icon="pi pi-filter-slash" label="Limpiar" severity="secondary" @click="limpiarFiltros" />
      </div>
    </template>
  </Toolbar>
</template>

<script setup lang="ts">
import { ref, onUnmounted, onMounted } from "vue";
import DatePicker from 'primevue/datepicker';
import Toolbar from 'primevue/toolbar';
import Select from "primevue/select";
import Button from "primevue/button";
import axios from "axios";
import { useKardexGeneralStore } from './kardexGeneralStore';

const kardexGeneralStore = useKardexGeneralStore();

const dates = ref();
const productoSeleccionado = ref(null);
const productos = ref([]);
const timeoutRef = ref(null);

// Variables para sub-branches
const subBranchSeleccionada = ref(null);
const subBranches = ref([]);

// Variables para tipos de movimiento y categorías
const movementTypeSeleccionado = ref(null);
const movementTypes = ref([
  { label: 'Entrada', value: 'entrada' },
  { label: 'Salida', value: 'salida' }
]);

const movementCategorySeleccionado = ref(null);
const movementCategories = ref([
  { label: 'Compra', value: 'compra' },
  { label: 'Venta', value: 'venta' },
  { label: 'Ajuste', value: 'ajuste' },
  { label: 'Devolución', value: 'devolucion' },
  { label: 'Traslado', value: 'traslado' }
]);

const onFiltroChange = () => {
  console.log("Filtro cambiado");
  buscarKardexGeneral();
};

const onFechasSeleccionadas = () => {
  console.log("Fechas seleccionadas:", dates.value);
  if (dates.value && dates.value.length === 2 && dates.value[0] && dates.value[1]) {
    buscarKardexGeneral();
  } else if (!dates.value || dates.value.length === 0) {
    buscarKardexGeneral();
  }
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

// Función para buscar kardex general
const buscarKardexGeneral = async () => {
  const params: any = {};

  // Agregar producto si está seleccionado
  if (productoSeleccionado.value) {
    params.product_id = productoSeleccionado.value;
  }

  // Agregar fechas si están seleccionadas
  if (dates.value && dates.value[0] && dates.value[1]) {
    const formatDate = (date) => {
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const day = String(date.getDate()).padStart(2, '0');
      return `${year}-${month}-${day}`;
    };

    params.fecha_inicio = formatDate(dates.value[0]);
    params.fecha_fin = formatDate(dates.value[1]);
  }

  // Agregar sucursal si está seleccionada
  if (subBranchSeleccionada.value) {
    params.sub_branch_id = subBranchSeleccionada.value;
  }

  // Agregar tipo de movimiento si está seleccionado
  if (movementTypeSeleccionado.value) {
    params.movement_type = movementTypeSeleccionado.value;
  }

  // Agregar categoría de movimiento si está seleccionada
  if (movementCategorySeleccionado.value) {
    params.movement_category = movementCategorySeleccionado.value;
  }

  console.log("Buscando kardex general con parámetros:", params);
  
  // Cargar los datos en el store
  await kardexGeneralStore.fetchKardexGeneral(params);
};

// Función para limpiar filtros
const limpiarFiltros = () => {
  productoSeleccionado.value = null;
  dates.value = null;
  subBranchSeleccionada.value = null;
  movementTypeSeleccionado.value = null;
  movementCategorySeleccionado.value = null;
  productos.value = [];
  
  // Buscar con filtros vacíos (todos los registros)
  buscarKardexGeneral();
};

// Función para cargar las sub-branches
const cargarSubBranches = async () => {
  try {
    const response = await axios.get("/sub-branches/search");
    subBranches.value = response.data.data;
    
    console.log("Sub-branches cargadas:", subBranches.value.length);
  } catch (error) {
    console.error('Error al cargar sub-branches:', error);
  }
};

onMounted(() => {
  cargarSubBranches();
  // Cargar datos iniciales sin filtros
  buscarKardexGeneral();
});

onUnmounted(() => {
  if (timeoutRef.value) {
    clearTimeout(timeoutRef.value);
  }
});
</script>
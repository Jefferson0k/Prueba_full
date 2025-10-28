<template>
    <!-- Sección de Totales -->
    <div class="mb-6">
        <!-- Total General - Grande y destacado -->
        <div class="mb-4">
            <Message severity="info" class="border-round-lg shadow-lg">
                <template #icon>
                    <div class="bg-blue-100 border-round-lg p-3">
                        <i class="pi pi-wallet text-4xl text-blue-600"></i>
                    </div>
                </template>
                <div class="ml-3 flex-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-600 font-medium text-lg mb-1">Total General</div>
                            <div class="text-4xl font-bold text-900">S/. {{ formatCurrency(totalGeneral) }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-600">{{ products.length }} registros</div>
                        </div>
                    </div>
                </div>
            </Message>
        </div>

        <!-- Métodos de Pago - Más pequeños y seleccionables -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            <Message 
                v-for="method in paymentMethods" 
                :key="method.id"
                @click="toggleMethodFilter(method.id)"
                :severity="selectedMethodId === method.id ? getMethodSeverity(method.code) : 'secondary'"
                :class="[
                    'border-round-lg cursor-pointer transition-all duration-300 relative',
                    selectedMethodId === method.id 
                        ? 'shadow-lg scale-105 ring-2 ring-offset-2' + getRingColor(method.code)
                        : 'shadow-sm hover:shadow-md'
                ]">
                <template #icon>
                    <div :class="[
                        'border-round-lg p-2',
                        selectedMethodId === method.id ? 'bg-white bg-opacity-20' : ''
                    ]">
                        <i :class="getMethodIcon(method.code) + ' text-2xl'"></i>
                    </div>
                </template>
                <div class="ml-2 flex-1">
                    <div class="flex flex-col">
                        <div class="font-semibold text-sm mb-1">{{ method.name }}</div>
                        <div class="text-xl font-bold">S/. {{ formatCurrency(methodTotals[method.id] || 0) }}</div>
                        <div v-if="selectedMethodId === method.id" class="text-xs opacity-90 mt-1">
                            {{ getMethodCount(method.id) }} registros
                        </div>
                    </div>
                </div>
                <!-- Checkmark cuando está seleccionado -->
                <div v-if="selectedMethodId === method.id" 
                     class="absolute top-2 right-2 bg-white border-round-circle w-6 h-6 flex items-center justify-center">
                    <i class="pi pi-check text-xs font-bold" :class="getCheckColor(method.code)"></i>
                </div>
            </Message>
        </div>
    </div>

    <!-- Tabla de Habitaciones -->
    <DataTable 
        ref="dt" 
        v-model:selection="selectedProducts" 
        :value="filteredProducts" 
        dataKey="id" 
        :paginator="true" 
        :rows="10"
        :filters="filters"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[5, 10, 25]"
        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} habitaciones" 
        class="p-datatable-sm">
        <template #header>
            <div class="flex flex-wrap gap-2 items-center justify-between">
                <div class="flex items-center gap-3">
                    <h4 class="m-0">Habitaciones</h4>
                    <span v-if="selectedMethodId" 
                          class="bg-primary text-white px-3 py-1 border-round-lg text-sm font-semibold flex items-center gap-2">
                        <i :class="getMethodIcon(getSelectedMethod()?.code)"></i>
                        {{ getSelectedMethodName() }}
                        <i @click.stop="selectedMethodId = null" 
                           class="pi pi-times cursor-pointer hover:text-gray-200"></i>
                    </span>
                </div>
                <IconField>
                    <InputIcon>
                        <i class="pi pi-search" />
                    </InputIcon>
                    <InputText v-model="filters['global'].value" placeholder="Buscar..." />
                </IconField>
            </div>
        </template>
        <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>
        <Column field="code" header="Codigo" sortable style="min-width: 12rem"></Column>
        <Column field="name" header="Tipo" sortable style="min-width: 16rem"></Column>
        <Column field="name" header="Tarifa" sortable style="min-width: 16rem"></Column>
        <Column field="name" header="Moneda" sortable style="min-width: 16rem"></Column>
        <Column field="name" header="Sub. Total H." sortable style="min-width: 16rem"></Column>
        <Column field="name" header="Sub. Total P." sortable style="min-width: 16rem"></Column>
        <Column field="name" header="Monto" sortable style="min-width: 16rem"></Column>
        <Column field="name" header="Estado" sortable style="min-width: 16rem"></Column>
        <Column field="name" header="Creacion" sortable style="min-width: 16rem"></Column>
        <Column field="name" header="Creador Por" sortable style="min-width: 16rem"></Column>
        <Column field="name" header="Acciones" sortable style="min-width: 16rem"></Column>
    </DataTable>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Message from 'primevue/message';

const filters = ref({
    'global': { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const selectedProducts = ref();
const products = ref([]);
const paymentMethods = ref([]);
const methodTotals = ref({});
const totalGeneral = ref(0);
const selectedMethodId = ref(null);

// Función para obtener métodos de pago
const fetchPaymentMethods = async () => {
    try {
        const response = await fetch('/payments/methods');
        const result = await response.json();
        
        if (result.success) {
            paymentMethods.value = result.data
                .filter(method => method.is_active)
                .sort((a, b) => a.sort_order - b.sort_order);
        }
    } catch (error) {
        console.error('Error al cargar métodos de pago:', error);
    }
};

// Función para obtener el ícono según el método de pago
const getMethodIcon = (code: string): string => {
    const icons = {
        'cash': 'pi pi-money-bill',
        'debit_card': 'pi pi-credit-card',
        'credit_card': 'pi pi-credit-card',
        'yape': 'pi pi-mobile',
        'plin': 'pi pi-mobile',
        'transfer': 'pi pi-send'
    };
    return icons[code] || 'pi pi-wallet';
};

// Función para obtener el severity según el método
const getMethodSeverity = (code: string): string => {
    const severities = {
        'cash': 'success',
        'debit_card': 'warn',
        'credit_card': 'warn',
        'yape': 'secondary',
        'plin': 'info',
        'transfer': 'contrast'
    };
    return severities[code] || 'secondary';
};

// Función para obtener el color del ring
const getRingColor = (code: string): string => {
    const colors = {
        'cash': ' ring-green-500',
        'debit_card': ' ring-orange-500',
        'credit_card': ' ring-orange-500',
        'yape': ' ring-purple-500',
        'plin': ' ring-blue-500',
        'transfer': ' ring-gray-700'
    };
    return colors[code] || ' ring-gray-500';
};

// Función para obtener el color del check
const getCheckColor = (code: string): string => {
    const colors = {
        'cash': 'text-green-600',
        'debit_card': 'text-orange-600',
        'credit_card': 'text-orange-600',
        'yape': 'text-purple-600',
        'plin': 'text-blue-600',
        'transfer': 'text-gray-700'
    };
    return colors[code] || 'text-gray-600';
};

// Función para formatear moneda
const formatCurrency = (value: number): string => {
    return value.toFixed(2);
};

// Toggle filtro por método de pago
const toggleMethodFilter = (methodId: string) => {
    if (selectedMethodId.value === methodId) {
        selectedMethodId.value = null;
    } else {
        selectedMethodId.value = methodId;
    }
};

// Obtener método seleccionado
const getSelectedMethod = () => {
    return paymentMethods.value.find(m => m.id === selectedMethodId.value);
};

// Obtener nombre del método seleccionado
const getSelectedMethodName = (): string => {
    const method = getSelectedMethod();
    return method ? method.name : '';
};

// Obtener cantidad de registros por método
const getMethodCount = (methodId: string): number => {
    return products.value.filter(p => p.payment_method_id === methodId).length;
};

// Productos filtrados según el método de pago seleccionado
const filteredProducts = computed(() => {
    if (!selectedMethodId.value) {
        return products.value;
    }
    return products.value.filter(product => product.payment_method_id === selectedMethodId.value);
});

// Calcular totales por método de pago
const calculateTotals = () => {
    methodTotals.value = {};
    totalGeneral.value = 0;
    
    products.value.forEach(product => {
        if (product.payment_method_id && product.amount) {
            if (!methodTotals.value[product.payment_method_id]) {
                methodTotals.value[product.payment_method_id] = 0;
            }
            methodTotals.value[product.payment_method_id] += parseFloat(product.amount);
            totalGeneral.value += parseFloat(product.amount);
        }
    });
};

onMounted(async () => {
    await fetchPaymentMethods();
    // Cargar tus productos/habitaciones aquí
    calculateTotals();
});
</script>
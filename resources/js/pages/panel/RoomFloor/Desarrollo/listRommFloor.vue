<template>
    <div class="grid grid-cols-12 gap-6">
        <!-- Información Principal -->
        <div class="col-span-12 lg:col-span-8">
            <!-- Encabezado de la Habitación -->
            <div class="mb-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="flex items-center justify-center w-16 h-16 bg-primary-100 dark:bg-primary-900/30 rounded-lg border-2 border-primary-300 dark:border-primary-700">
                                <span class="text-2xl font-bold text-primary-700 dark:text-primary-300">
                                    {{ roomData?.room_number }}
                                </span>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-surface-900 dark:text-surface-0">
                                    Habitación {{ roomData?.room_number }}
                                </h2>
                                <p class="text-surface-600 dark:text-surface-400 text-sm mt-1">
                                    {{ roomData?.full_name }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <Tag 
                            :value="getStatusLabel(roomData?.status)" 
                            :severity="getStatusSeverity(roomData?.status)"
                            class="text-sm"
                        />
                        <Badge 
                            :value="roomData?.is_active ? 'Activa' : 'Inactiva'" 
                            :severity="roomData?.is_active ? 'success' : 'secondary'"
                        />
                    </div>
                </div>

                <!-- Información del Piso y Tipo -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="p-4 bg-surface-50 dark:bg-surface-800 rounded-lg border border-surface-200 dark:border-surface-700">
                        <div class="flex items-center gap-3">
                            <i class="pi pi-building text-2xl text-primary-500"></i>
                            <div>
                                <p class="text-sm text-surface-600 dark:text-surface-400">Piso</p>
                                <p class="font-semibold text-surface-900 dark:text-surface-0">
                                    {{ roomData?.floor?.name }}
                                </p>
                                <p class="text-xs text-surface-500 dark:text-surface-400">
                                    Nivel {{ roomData?.floor?.floor_number }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-surface-50 dark:bg-surface-800 rounded-lg border border-surface-200 dark:border-surface-700">
                        <div class="flex items-center gap-3">
                            <i class="pi pi-home text-2xl text-primary-500"></i>
                            <div>
                                <p class="text-sm text-surface-600 dark:text-surface-400">Tipo de Habitación</p>
                                <p class="font-semibold text-surface-900 dark:text-surface-0">
                                    {{ roomData?.room_type?.name }}
                                </p>
                                <p class="text-xs text-surface-500 dark:text-surface-400">
                                    Capacidad: {{ roomData?.room_type?.capacity }} persona(s)
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Selector de Tarifa -->
            <div class="mb-6">
                <div class="p-5 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl border-2 border-blue-200 dark:border-blue-700">
                    <h3 class="text-lg font-bold text-surface-900 dark:text-surface-0 mb-4 flex items-center gap-2">
                        <i class="pi pi-money-bill"></i>
                        Seleccionar Tarifa
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div 
                            @click="selectRate('hour')"
                            :class="[
                                'p-4 rounded-lg border-2 cursor-pointer transition-all',
                                selectedRate === 'hour' 
                                    ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/30 shadow-lg' 
                                    : 'border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 hover:border-primary-300'
                            ]"
                        >
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-surface-600 dark:text-surface-400">Por Hora</span>
                                <i v-if="selectedRate === 'hour'" class="pi pi-check-circle text-primary-500"></i>
                            </div>
                            <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                S/ {{ roomData?.room_type?.base_price_per_hour }}
                            </p>
                        </div>

                        <div 
                            @click="selectRate('day')"
                            :class="[
                                'p-4 rounded-lg border-2 cursor-pointer transition-all',
                                selectedRate === 'day' 
                                    ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/30 shadow-lg' 
                                    : 'border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 hover:border-primary-300'
                            ]"
                        >
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-surface-600 dark:text-surface-400">Por Día</span>
                                <i v-if="selectedRate === 'day'" class="pi pi-check-circle text-primary-500"></i>
                            </div>
                            <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                S/ {{ roomData?.room_type?.base_price_per_day }}
                            </p>
                        </div>

                        <div 
                            @click="selectRate('night')"
                            :class="[
                                'p-4 rounded-lg border-2 cursor-pointer transition-all',
                                selectedRate === 'night' 
                                    ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/30 shadow-lg' 
                                    : 'border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 hover:border-primary-300'
                            ]"
                        >
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-surface-600 dark:text-surface-400">Por Noche</span>
                                <i v-if="selectedRate === 'night'" class="pi pi-check-circle text-primary-500"></i>
                            </div>
                            <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                S/ {{ roomData?.room_type?.base_price_per_night }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Registro de Cliente -->
            <div class="mb-6">
                <div class="p-5 bg-surface-50 dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-surface-900 dark:text-surface-0 flex items-center gap-2">
                            <i class="pi pi-user"></i>
                            Cliente
                        </h3>
                        <Button 
                            label="Registrar Cliente" 
                            icon="pi pi-user-plus" 
                            severity="info"
                            size="small"
                            @click="showClientDialog = true"
                            :disabled="isTimerRunning"
                        />
                    </div>
                    
                    <div v-if="selectedClient" class="p-4 bg-white dark:bg-surface-700 rounded-lg border border-surface-300 dark:border-surface-600">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center">
                                    <i class="pi pi-user text-primary-600 dark:text-primary-400 text-xl"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-surface-900 dark:text-surface-0">{{ selectedClient.name }}</p>
                                    <p class="text-sm text-surface-600 dark:text-surface-400">{{ selectedClient.document }}</p>
                                </div>
                            </div>
                            <Button 
                                icon="pi pi-times" 
                                severity="danger"
                                text
                                rounded
                                @click="removeClient"
                                :disabled="isTimerRunning"
                            />
                        </div>
                    </div>
                    <div v-else class="text-center py-6 text-surface-500 dark:text-surface-400">
                        <i class="pi pi-user-plus text-4xl mb-2"></i>
                        <p class="text-sm">No hay cliente registrado</p>
                    </div>
                </div>
            </div>

            <!-- Productos Adicionales -->
            <div class="mb-6">
                <div class="p-5 bg-surface-50 dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-surface-900 dark:text-surface-0 flex items-center gap-2">
                            <i class="pi pi-shopping-cart"></i>
                            Productos Adicionales
                        </h3>
                        <Button 
                            label="Agregar Producto" 
                            icon="pi pi-plus" 
                            severity="success"
                            size="small"
                            @click="showProductDialog = true"
                        />
                    </div>

                    <DataTable 
                        :value="products" 
                        :paginator="false"
                        class="p-datatable-sm"
                        v-if="products.length > 0"
                    >
                        <Column field="name" header="Producto">
                            <template #body="{ data }">
                                <div class="flex items-center gap-2">
                                    <i class="pi pi-box text-primary-500"></i>
                                    <span class="font-medium">{{ data.name }}</span>
                                </div>
                            </template>
                        </Column>
                        <Column field="quantity" header="Cantidad">
                            <template #body="{ data }">
                                <Badge :value="data.quantity" severity="info" />
                            </template>
                        </Column>
                        <Column field="price" header="Precio Unit.">
                            <template #body="{ data }">
                                <span class="font-semibold text-green-600 dark:text-green-400">
                                    S/ {{ data.price }}
                                </span>
                            </template>
                        </Column>
                        <Column header="Subtotal">
                            <template #body="{ data }">
                                <span class="font-bold text-surface-900 dark:text-surface-0">
                                    S/ {{ (data.quantity * data.price).toFixed(2) }}
                                </span>
                            </template>
                        </Column>
                        <Column header="Acciones">
                            <template #body="{ data }">
                                <Button 
                                    icon="pi pi-trash" 
                                    severity="danger"
                                    text
                                    rounded
                                    size="small"
                                    @click="removeProduct(data.id)"
                                />
                            </template>
                        </Column>
                    </DataTable>

                    <div v-else class="text-center py-6 text-surface-500 dark:text-surface-400">
                        <i class="pi pi-shopping-cart text-4xl mb-2"></i>
                        <p class="text-sm">No hay productos agregados</p>
                    </div>
                </div>
            </div>

            <!-- Resumen / Boleta -->
            <div class="mb-6">
                <div class="p-6 bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 rounded-xl border-2 border-slate-300 dark:border-slate-600">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-surface-900 dark:text-surface-0 flex items-center gap-2">
                            <i class="pi pi-file-edit"></i>
                            Resumen de Cuenta
                        </h3>
                        
                        <!-- Botones de Tipo de Comprobante -->
                        <div class="flex gap-2">
                            <Button 
                                :label="voucherType === 'boleta' ? '✓ Boleta' : 'Boleta'" 
                                :severity="voucherType === 'boleta' ? 'success' : 'secondary'"
                                size="small"
                                @click="voucherType = 'boleta'"
                                :outlined="voucherType !== 'boleta'"
                            />
                            <Button 
                                :label="voucherType === 'ticket' ? '✓ Ticket' : 'Ticket'" 
                                :severity="voucherType === 'ticket' ? 'success' : 'secondary'"
                                size="small"
                                @click="voucherType = 'ticket'"
                                :outlined="voucherType !== 'ticket'"
                            />
                            <Button 
                                :label="voucherType === 'factura' ? '✓ Factura' : 'Factura'" 
                                :severity="voucherType === 'factura' ? 'success' : 'secondary'"
                                size="small"
                                @click="voucherType = 'factura'"
                                :outlined="voucherType !== 'factura'"
                            />
                        </div>
                    </div>

                    <!-- Indicador de tipo de comprobante seleccionado -->
                    <div class="mb-4 p-3 bg-primary-50 dark:bg-primary-900/20 rounded-lg border border-primary-200 dark:border-primary-700">
                        <div class="flex items-center gap-2">
                            <i class="pi pi-receipt text-primary-600 dark:text-primary-400"></i>
                            <span class="font-semibold text-primary-700 dark:text-primary-300">
                                Comprobante: {{ voucherType.toUpperCase() }}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <!-- Habitación -->
                        <div class="flex justify-between items-center pb-2 border-b border-surface-300 dark:border-surface-600">
                            <div>
                                <p class="font-medium text-surface-900 dark:text-surface-0">
                                    Habitación {{ roomData?.room_number }}
                                </p>
                                <p class="text-sm text-surface-600 dark:text-surface-400">
                                    {{ getRateLabel(selectedRate) }} - {{ timeAmount }} {{ getTimeUnit(selectedRate) }}
                                </p>
                            </div>
                            <span class="font-semibold text-lg text-surface-900 dark:text-surface-0">
                                S/ {{ calculateRoomTotal() }}
                            </span>
                        </div>

                        <!-- Productos -->
                        <div v-if="products.length > 0" class="pb-2 border-b border-surface-300 dark:border-surface-600">
                            <p class="font-medium text-surface-900 dark:text-surface-0 mb-2">Productos</p>
                            <div v-for="product in products" :key="product.id" class="flex justify-between text-sm mb-1">
                                <span class="text-surface-600 dark:text-surface-400">
                                    {{ product.name }} x{{ product.quantity }}
                                </span>
                                <span class="text-surface-900 dark:text-surface-0">
                                    S/ {{ (product.quantity * product.price).toFixed(2) }}
                                </span>
                            </div>
                        </div>

                        <!-- Subtotal -->
                        <div class="flex justify-between items-center text-lg">
                            <span class="font-medium text-surface-700 dark:text-surface-300">Subtotal:</span>
                            <span class="font-semibold text-surface-900 dark:text-surface-0">
                                S/ {{ calculateSubtotal() }}
                            </span>
                        </div>

                        <!-- Total -->
                        <div class="flex justify-between items-center pt-3 border-t-2 border-surface-400 dark:border-surface-500">
                            <span class="text-2xl font-bold text-surface-900 dark:text-surface-0">TOTAL:</span>
                            <span class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                                S/ {{ calculateTotal() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Lateral - Cronómetro y Acción -->
        <div class="col-span-12 lg:col-span-4">
            <div class="sticky top-6">
                <!-- Estado Actual -->
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-surface-900 dark:text-surface-0 mb-2">
                        Estado de la Habitación
                    </h3>
                    <div class="inline-flex items-center justify-center w-full">
                        <Tag 
                            :value="getStatusLabel(roomData?.status)" 
                            :severity="getStatusSeverity(roomData?.status)"
                            class="text-lg px-6 py-3"
                        />
                    </div>
                </div>

                <!-- Cronómetro REGRESIVO -->
                <div class="bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 p-8 rounded-xl border-2 border-primary-200 dark:border-primary-700 mb-6">
                    <div class="text-center">
                        <i :class="[
                            'pi pi-clock text-4xl mb-4',
                            isTimerRunning && remainingSeconds <= 300 ? 'text-red-600 dark:text-red-400 animate-pulse' : 'text-primary-600 dark:text-primary-400'
                        ]"></i>
                        <p class="text-sm text-surface-600 dark:text-surface-400 mb-2">
                            {{ isTimerRunning ? 'Tiempo Restante' : 'Tiempo a Contratar' }}
                        </p>
                        <div :class="[
                            'font-mono text-5xl font-bold mb-2',
                            isTimerRunning && remainingSeconds <= 300 ? 'text-red-700 dark:text-red-300' : 'text-primary-700 dark:text-primary-300'
                        ]">
                            {{ formattedTime }}
                        </div>
                        <p class="text-xs text-surface-500 dark:text-surface-400">
                            {{ isTimerRunning ? (remainingSeconds <= 0 ? '¡Tiempo agotado!' : 'En curso') : 'Sin actividad' }}
                        </p>
                        
                        <!-- Barra de progreso -->
                        <div v-if="isTimerRunning" class="mt-4">
                            <div class="w-full bg-surface-300 dark:bg-surface-600 rounded-full h-2">
                                <div 
                                    :class="[
                                        'h-2 rounded-full transition-all duration-1000',
                                        remainingSeconds <= 300 ? 'bg-red-500' : 'bg-primary-500'
                                    ]"
                                    :style="{ width: `${progressPercentage}%` }"
                                ></div>
                            </div>
                            <p class="text-xs mt-2 text-surface-600 dark:text-surface-400">
                                {{ progressPercentage.toFixed(1) }}% del tiempo restante
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Control de Tiempo -->
                <div class="mb-4 p-4 bg-surface-50 dark:bg-surface-800 rounded-lg border border-surface-200 dark:border-surface-700">
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                        Cantidad de Tiempo
                    </label>
                    <div class="flex gap-2">
                        <InputNumber 
                            v-model="timeAmount" 
                            :min="1"
                            :max="24"
                            showButtons
                            class="flex-1"
                            :disabled="isTimerRunning"
                        />
                        <Button 
                            :label="getTimeUnit(selectedRate)" 
                            severity="secondary"
                            disabled
                        />
                    </div>
                </div>

                <!-- Botón Empezar/Detener -->
                <Button 
                    v-if="!isTimerRunning"
                    label="Iniciar Servicio" 
                    icon="pi pi-play" 
                    severity="success"
                    size="large"
                    class="w-full mb-4"
                    :disabled="roomData?.status !== 'available' || !selectedClient || !selectedRate"
                    @click="startService"
                />
                <Button 
                    v-else
                    label="Finalizar Servicio" 
                    icon="pi pi-stop" 
                    severity="danger"
                    size="large"
                    class="w-full mb-4"
                    @click="stopService"
                />

                <!-- Información Rápida -->
                <div class="bg-surface-50 dark:bg-surface-800 p-4 rounded-lg border border-surface-200 dark:border-surface-700">
                    <h4 class="font-semibold mb-3 text-surface-900 dark:text-surface-0">
                        <i class="pi pi-info-circle mr-2"></i>Información Rápida
                    </h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-surface-600 dark:text-surface-400">Tipo:</span>
                            <span class="font-semibold text-surface-900 dark:text-surface-0">
                                {{ roomData?.room_type?.name }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-surface-600 dark:text-surface-400">Capacidad:</span>
                            <span class="font-semibold text-surface-900 dark:text-surface-0">
                                {{ roomData?.room_type?.capacity }} persona(s)
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-surface-600 dark:text-surface-400">Tarifa:</span>
                            <span class="font-semibold text-green-600 dark:text-green-400">
                                {{ selectedRate ? getRateLabel(selectedRate) : 'No seleccionada' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-surface-600 dark:text-surface-400">Comprobante:</span>
                            <span class="font-semibold text-primary-600 dark:text-primary-400">
                                {{ voucherType.toUpperCase() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dialog Registrar Cliente -->
    <Dialog 
        v-model:visible="showClientDialog" 
        modal 
        header="Registrar Cliente"
        :style="{ width: '500px' }"
    >
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-2">Nombre Completo *</label>
                <InputText v-model="clientForm.name" placeholder="Ej: Juan Pérez" class="w-full" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Documento *</label>
                <InputText v-model="clientForm.document" placeholder="Ej: 12345678" class="w-full" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Teléfono (opcional)</label>
                <InputText v-model="clientForm.phone" placeholder="Ej: 987654321" class="w-full" />
            </div>
        </div>

        <template #footer>
            <Button label="Cancelar" severity="secondary" @click="showClientDialog = false" />
            <Button label="Guardar Cliente" icon="pi pi-check" @click="saveClient" />
        </template>
    </Dialog>

    <!-- Dialog Agregar Producto -->
    <Dialog 
        v-model:visible="showProductDialog" 
        modal 
        header="Agregar Producto"
        :style="{ width: '500px' }"
    >
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-2">Nombre del Producto *</label>
                <InputText v-model="productForm.name" placeholder="Ej: Bebida, Snack, etc." class="w-full" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Cantidad *</label>
                <InputNumber v-model="productForm.quantity" :min="1" showButtons class="w-full" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Precio Unitario *</label>
                <InputNumber v-model="productForm.price" mode="currency" currency="PEN" locale="es-PE" class="w-full" />
            </div>
        </div>

        <template #footer>
            <Button label="Cancelar" severity="secondary" @click="showProductDialog = false" />
            <Button label="Agregar Producto" icon="pi pi-check" @click="saveProduct" />
        </template>
    </Dialog>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import Badge from 'primevue/badge';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';

interface Props {
  roomData?: any;
}

const props = defineProps<Props>();

// Estados
const selectedRate = ref<'hour' | 'day' | 'night' | null>(null);
const selectedClient = ref<any>(null);
const products = ref<any[]>([]);
const showClientDialog = ref(false);
const showProductDialog = ref(false);
const isTimerRunning = ref(false);
const remainingSeconds = ref(0);
const totalSeconds = ref(0);
const timerInterval = ref<any>(null);
const timeAmount = ref(1);
const voucherType = ref<'boleta' | 'ticket' | 'factura'>('boleta');

// Formularios
const clientForm = ref({
    name: '',
    document: '',
    phone: ''
});

const productForm = ref({
    name: '',
    quantity: 1,
    price: 0
});

// Métodos
const selectRate = (rate: 'hour' | 'day' | 'night') => {
    if (!isTimerRunning.value) {
        selectedRate.value = rate;
    }
};

const getRateLabel = (rate: string | null) => {
    const labels: Record<string, string> = {
        'hour': 'Por Hora',
        'day': 'Por Día',
        'night': 'Por Noche'
    };
    return rate ? labels[rate] : '';
};

const getTimeUnit = (rate: string | null) => {
    const units: Record<string, string> = {
        'hour': 'Hora(s)',
        'day': 'Día(s)',
        'night': 'Noche(s)'
    };
    return rate ? units[rate] : '';
};

const calculateTotalSeconds = () => {
    if (!selectedRate.value) return 0;
    
    const multipliers: Record<string, number> = {
        'hour': 3600,      // 1 hora = 3600 segundos
        'day': 86400,      // 1 día = 86400 segundos
        'night': 28800     // 1 noche = 8 horas = 28800 segundos
    };
    
    return timeAmount.value * multipliers[selectedRate.value];
};

const saveClient = () => {
    if (!clientForm.value.name || !clientForm.value.document) {
        alert('Por favor complete los campos obligatorios');
        return;
    }
    
    selectedClient.value = {
        name: clientForm.value.name,
        document: clientForm.value.document,
        phone: clientForm.value.phone
    };
    
    showClientDialog.value = false;
    clientForm.value = { name: '', document: '', phone: '' };
};

const removeClient = () => {
    if (!isTimerRunning.value) {
        selectedClient.value = null;
    }
};

const saveProduct = () => {
    if (!productForm.value.name || productForm.value.price <= 0) {
        alert('Por favor complete todos los campos correctamente');
        return;
    }
    
    products.value.push({
        id: Date.now(),
        name: productForm.value.name,
        quantity: productForm.value.quantity,
        price: productForm.value.price
    });
    
    showProductDialog.value = false;
    productForm.value = { name: '', quantity: 1, price: 0 };
};

const removeProduct = (id: number) => {
    products.value = products.value.filter(p => p.id !== id);
};

const calculateRoomTotal = () => {
    if (!selectedRate.value || !props.roomData?.room_type) return '0.00';
    
    const rates: Record<string, string> = {
        'hour': props.roomData.room_type.base_price_per_hour,
        'day': props.roomData.room_type.base_price_per_day,
        'night': props.roomData.room_type.base_price_per_night
    };
    
    const basePrice = parseFloat(rates[selectedRate.value] || '0');
    return (basePrice * timeAmount.value).toFixed(2);
};

const calculateSubtotal = () => {
    const roomTotal = parseFloat(calculateRoomTotal());
    const productsTotal = products.value.reduce((sum, p) => sum + (p.quantity * p.price), 0);
    return (roomTotal + productsTotal).toFixed(2);
};

const calculateTotal = () => {
    return calculateSubtotal();
};

const formattedTime = computed(() => {
    const hours = Math.floor(remainingSeconds.value / 3600);
    const minutes = Math.floor((remainingSeconds.value % 3600) / 60);
    const seconds = remainingSeconds.value % 60;
    
    return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
});

const progressPercentage = computed(() => {
    if (totalSeconds.value === 0) return 0;
    return (remainingSeconds.value / totalSeconds.value) * 100;
});

const startService = () => {
    if (!selectedClient.value) {
        alert('Debe registrar un cliente primero');
        return;
    }
    if (!selectedRate.value) {
        alert('Debe seleccionar una tarifa');
        return;
    }
    
    totalSeconds.value = calculateTotalSeconds();
    remainingSeconds.value = totalSeconds.value;
    isTimerRunning.value = true;
    
    // Contador regresivo
    timerInterval.value = setInterval(() => {
        if (remainingSeconds.value > 0) {
            remainingSeconds.value--;
        } else {
            // Tiempo agotado
            clearInterval(timerInterval.value);
            alert('¡El tiempo ha terminado!');
        }
    }, 1000);
};

const stopService = () => {
    isTimerRunning.value = false;
    if (timerInterval.value) {
        clearInterval(timerInterval.value);
        timerInterval.value = null;
    }
    
    const confirmMessage = `
Servicio finalizado.
Comprobante: ${voucherType.value.toUpperCase()}
Cliente: ${selectedClient.value.name}
Total a pagar: S/ ${calculateTotal()}
    `;
    
    if (confirm(confirmMessage + '\n\n¿Desea procesar el pago?')) {
        // Aquí puedes agregar la lógica para procesar el pago
        console.log('Procesando pago...');
        console.log('Tipo de comprobante:', voucherType.value);
        console.log('Total:', calculateTotal());
        
        // Resetear todo
        resetService();
    }
};

const resetService = () => {
    selectedRate.value = null;
    selectedClient.value = null;
    products.value = [];
    timeAmount.value = 1;
    remainingSeconds.value = 0;
    totalSeconds.value = 0;
    voucherType.value = 'boleta';
};

const getStatusLabel = (status: string) => {
    const labels: Record<string, string> = {
        'available': 'Disponible',
        'occupied': 'Ocupada',
        'maintenance': 'Mantenimiento',
        'cleaning': 'Limpieza'
    };
    return labels[status] || status;
};

const getStatusSeverity = (status: string) => {
    const severities: Record<string, string> = {
        'available': 'success',
        'occupied': 'danger',
        'maintenance': 'warn',
        'cleaning': 'info'
    };
    return severities[status] || 'secondary';
};

// Observar cambios en timeAmount para recalcular el tiempo
watch(timeAmount, () => {
    if (!isTimerRunning.value && selectedRate.value) {
        remainingSeconds.value = calculateTotalSeconds();
    }
});

// Observar cambios en selectedRate
watch(selectedRate, () => {
    if (!isTimerRunning.value) {
        remainingSeconds.value = calculateTotalSeconds();
    }
});

onMounted(() => {
    // Inicializar con el tiempo calculado
    if (selectedRate.value) {
        remainingSeconds.value = calculateTotalSeconds();
    }
});

onUnmounted(() => {
    if (timerInterval.value) {
        clearInterval(timerInterval.value);
    }
});
</script>

<style scoped>
/* Animación para el cronómetro */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.font-mono {
    font-family: 'Courier New', monospace;
}

/* Transiciones suaves */
.transition-all {
    transition: all 0.3s ease;
}
</style>
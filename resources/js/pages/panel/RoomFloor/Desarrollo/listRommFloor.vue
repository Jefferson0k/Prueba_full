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
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <!-- Piso -->
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

                    <!-- Tipo de Habitación -->
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

                    <!-- Moneda seleccionada -->
                    <div 
                        v-if="selectedCurrency"
                        class="p-4 bg-surface-50 dark:bg-surface-800 rounded-lg border-2 cursor-pointer transition-all border-green-500 bg-green-50 dark:bg-green-900/30 shadow-lg"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <i class="pi pi-dollar text-2xl text-primary-500"></i>
                                <div>
                                    <p class="text-sm text-surface-600 dark:text-surface-400">Moneda</p>
                                    <p class="font-semibold text-surface-900 dark:text-surface-0">
                                        {{ selectedCurrency?.name }}
                                    </p>
                                    <p class="text-xs text-surface-500 dark:text-surface-400">
                                        {{ selectedCurrency?.code }} — {{ selectedCurrency?.symbol }}
                                    </p>
                                </div>
                            </div>
                            <i class="pi pi-check-circle text-green-500 text-xl"></i>
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
                                {{ selectedCurrency?.symbol || 'S/' }} {{ roomData?.room_type?.base_price_per_hour }}
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
                                {{ selectedCurrency?.symbol || 'S/' }} {{ roomData?.room_type?.base_price_per_day }}
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
                                {{ selectedCurrency?.symbol || 'S/' }} {{ roomData?.room_type?.base_price_per_night }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Componente: Registro de Cliente -->
            <CustomerRegistration 
                v-model="selectedClient"
                :disabled="isTimerRunning"
                class="mb-6"
                @customer-saved="onCustomerSaved"
            />

            <!-- Componente: Productos Adicionales -->
            <ProductSales 
                v-model="products"
                :currency-symbol="selectedCurrency?.symbol || 'S/'"
                class="mb-6"
            />

            <!-- Componente: Resumen / Boleta -->
            <BillingSummary 
                :room-number="roomData?.room_number"
                :room-price="getCurrentRoomPrice()"
                :selected-rate="selectedRate"
                :time-amount="timeAmount"
                :products="products"
                :currency-symbol="selectedCurrency?.symbol || 'S/'"
                :currency-code="selectedCurrency?.code || 'PEN'"
                v-model="voucherType"
            />
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
                    :disabled="roomData?.status !== 'available' || !selectedClient || !selectedRate || !selectedCurrency"
                    @click="startService"
                />
                <Button 
                    v-else
                    label="Finalizar Servicio" 
                    icon="pi pi-stop" 
                    severity="danger"
                    size="large"
                    class="w-full mb-4"
                    @click="confirmStopService"
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
                            <span class="text-surface-600 dark:text-surface-400">Moneda:</span>
                            <span class="font-semibold text-green-600 dark:text-green-400">
                                {{ selectedCurrency?.symbol }} {{ selectedCurrency?.code }}
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
                        <div v-if="selectedClient" class="flex justify-between pt-2 border-t">
                            <span class="text-surface-600 dark:text-surface-400">Cliente ID:</span>
                            <span class="font-semibold text-blue-600 dark:text-blue-400">
                                {{ selectedClient?.id || 'Sin ID' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dialog Confirmación Finalizar -->
    <Dialog 
        v-model:visible="showStopDialog" 
        modal 
        header="Finalizar Servicio"
        :style="{ width: '500px' }"
    >
        <div class="space-y-4">
            <Message severity="info">
                ¿Desea finalizar el servicio y procesar el pago?
            </Message>

            <div class="p-4 bg-surface-50 dark:bg-surface-800 rounded-lg">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-surface-600 dark:text-surface-400">Cliente:</span>
                        <span class="font-semibold">{{ selectedClient?.name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-surface-600 dark:text-surface-400">Cliente ID:</span>
                        <span class="font-semibold text-blue-600">{{ selectedClient?.id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-surface-600 dark:text-surface-400">Habitación:</span>
                        <span class="font-semibold">{{ roomData?.room_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-surface-600 dark:text-surface-400">Tarifa:</span>
                        <span class="font-semibold">{{ getRateLabel(selectedRate) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-surface-600 dark:text-surface-400">Tiempo:</span>
                        <span class="font-semibold">{{ timeAmount }} {{ getTimeUnit(selectedRate) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-surface-600 dark:text-surface-400">Caja:</span>
                        <span class="font-semibold text-green-600">{{ userCashRegister?.name }}</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t">
                        <span class="text-lg font-bold">Total a pagar:</span>
                        <span class="text-lg font-bold text-primary-600 dark:text-primary-400">
                            {{ selectedCurrency?.symbol || 'S/' }} {{ calculateTotal() }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Solo Método de Pago -->
            <div class="p-4 bg-surface-50 dark:bg-surface-800 rounded-lg border border-surface-200 dark:border-surface-700">
                <h4 class="font-semibold mb-3 text-surface-900 dark:text-surface-0">
                    <i class="pi pi-credit-card mr-2"></i>Método de Pago
                </h4>
                
                <div class="grid grid-cols-2 gap-3">
                    <div 
                        v-for="method in paymentMethods" 
                        :key="method.id"
                        @click="selectedPaymentMethod = method"
                        :class="[
                            'p-3 rounded-lg border-2 cursor-pointer transition-all',
                            selectedPaymentMethod?.id === method.id 
                                ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/30 shadow-lg' 
                                : 'border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 hover:border-primary-300'
                        ]"
                    >
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-sm">{{ method.name }}</span>
                            <i v-if="selectedPaymentMethod?.id === method.id" class="pi pi-check-circle text-primary-500"></i>
                        </div>
                    </div>
                </div>

                <!-- Número de Operación -->
                <div v-if="selectedPaymentMethod?.requires_reference" class="mt-3">
                    <label class="block text-sm font-medium mb-2">
                        Número de Operación *
                    </label>
                    <InputText 
                        v-model="operationNumber" 
                        placeholder="Ingrese número de operación"
                        class="w-full"
                    />
                </div>
            </div>
        </div>

        <template #footer>
            <Button label="Cancelar" severity="secondary" @click="showStopDialog = false" />
            <Button 
                label="Procesar Pago" 
                icon="pi pi-check" 
                severity="success"
                @click="stopService"
                :loading="processingPayment"
                :disabled="!selectedPaymentMethod || (selectedPaymentMethod?.requires_reference && !operationNumber)"
            />
        </template>
    </Dialog>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import Badge from 'primevue/badge';
import Dialog from 'primevue/dialog';
import InputNumber from 'primevue/inputnumber';
import InputText from 'primevue/inputtext';
import Message from 'primevue/message';
import { useToast } from 'primevue/usetoast';
import axios from 'axios';

// Importar componentes modulares
import CustomerRegistration from './CustomerRegistration.vue';
import ProductSales from './ProductSales.vue';
import BillingSummary from './BillingSummary.vue';

interface Props {
    roomData?: any;
}

const props = defineProps<Props>();

const toast = useToast();

// Estados principales
const selectedRate = ref<'hour' | 'day' | 'night' | null>(null);
const selectedClient = ref<any>(null);
const products = ref<any[]>([]);
const isTimerRunning = ref(false);
const remainingSeconds = ref(0);
const totalSeconds = ref(0);
const timerInterval = ref<any>(null);
const timeAmount = ref(1);
const voucherType = ref<'boleta' | 'ticket' | 'factura'>('boleta');
const showStopDialog = ref(false);
const processingPayment = ref(false);
const currencies = ref<any[]>([]);
const selectedCurrency = ref<any>(null);
const rateTypes = ref<any[]>([]);
// Estados de pago
const paymentMethods = ref<any[]>([]);
const selectedPaymentMethod = ref<any>(null);
const userCashRegister = ref<any>(null);
const operationNumber = ref<string>('');

// Evento cuando se guarda un cliente
const onCustomerSaved = (customer: any) => {
    console.log('✅ Cliente guardado en listRommFloor:', customer);
    selectedClient.value = customer;
};

// Métodos de tarifa y tiempo
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

const getCurrentRoomPrice = () => {
    if (!selectedRate.value || !props.roomData?.room_type) return 0;
    const rates: Record<string, string> = {
        'hour': props.roomData.room_type.base_price_per_hour,
        'day': props.roomData.room_type.base_price_per_day,
        'night': props.roomData.room_type.base_price_per_night
    };
    return parseFloat(rates[selectedRate.value] || '0');
};

const calculateTotal = () => {
    const roomTotal = getCurrentRoomPrice() * timeAmount.value;
    const productsTotal = products.value.reduce((sum, p) => {
        const quantity = parseFloat(p.quantity || p.cantidad || 0);
        const price = parseFloat(p.precio_venta || p.price || 0);
        return sum + (quantity * price);
    }, 0);
    return (roomTotal + productsTotal).toFixed(2);
};

const calculateTotalSeconds = () => {
    if (!selectedRate.value) return 0;
    
    const multipliers: Record<string, number> = {
        'hour': 3600,
        'day': 86400,
        'night': 28800
    };
    
    return timeAmount.value * multipliers[selectedRate.value];
};

const calculateTotalHours = () => {
    switch (selectedRate.value) {
        case 'hour': return timeAmount.value;
        case 'day': return timeAmount.value * 24;
        case 'night': return timeAmount.value * 12;
        default: return 1;
    }
};

// Computed properties
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

const loadRateTypes = async () => {
    try {
        const response = await axios.get('/rate-types');
        rateTypes.value = response.data.data || response.data;
        console.log('Rate types cargados:', rateTypes.value);
    } catch (error: any) {
        console.error('Error al cargar rate types:', error);
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'No se pudieron cargar los tipos de tarifa',
            life: 3000
        });
    }
};

// Métodos del servicio
const startService = () => {
    if (!selectedClient.value) {
        toast.add({
            severity: 'warn',
            summary: 'Advertencia',
            detail: 'Debe registrar un cliente primero',
            life: 3000
        });
        return;
    }
    
    if (!selectedClient.value.id) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'El cliente no tiene un ID válido',
            life: 4000
        });
        console.error('❌ Cliente sin ID:', selectedClient.value);
        return;
    }
    
    if (!selectedRate.value) {
        toast.add({
            severity: 'warn',
            summary: 'Advertencia',
            detail: 'Debe seleccionar una tarifa',
            life: 3000
        });
        return;
    }
    if (!selectedCurrency.value) {
        toast.add({
            severity: 'warn',
            summary: 'Advertencia',
            detail: 'Debe seleccionar una moneda',
            life: 3000
        });
        return;
    }
    
    totalSeconds.value = calculateTotalSeconds();
    remainingSeconds.value = totalSeconds.value;
    isTimerRunning.value = true;
    
    toast.add({
        severity: 'success',
        summary: 'Servicio Iniciado',
        detail: `Cronómetro activado para ${timeAmount.value} ${getTimeUnit(selectedRate.value)}`,
        life: 3000
    });
    
    // Contador regresivo
    timerInterval.value = setInterval(() => {
        if (remainingSeconds.value > 0) {
            remainingSeconds.value--;
        } else {
            clearInterval(timerInterval.value);
            toast.add({
                severity: 'error',
                summary: '¡Tiempo Agotado!',
                detail: 'El tiempo del servicio ha terminado',
                life: 5000
            });
        }
    }, 1000);
};

const confirmStopService = async () => {
    try {
        // Cargar datos necesarios antes de mostrar el diálogo
        await loadNecessaryData();
        showStopDialog.value = true;
    } catch (error) {
        console.error('Error al cargar datos:', error);
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'No se pudieron cargar los datos necesarios',
            life: 4000
        });
    }
};

const stopService = async () => {
    console.log('========================================');
    console.log('🔍 VERIFICACIÓN ANTES DE ENVIAR BOOKING');
    console.log('========================================');
    console.log('selectedClient completo:', selectedClient.value);
    console.log('selectedClient.id:', selectedClient.value?.id);
    console.log('Tipo de ID:', typeof selectedClient.value?.id);
    console.log('========================================');

    // Validación mínima
    if (!selectedPaymentMethod.value) {
        toast.add({
            severity: 'warn',
            summary: 'Método de Pago Requerido',
            detail: 'Seleccione un método de pago',
            life: 3000
        });
        return;
    }

    if (selectedPaymentMethod.value?.requires_reference && !operationNumber.value.trim()) {
        toast.add({
            severity: 'warn',
            summary: 'Número de Operación Requerido',
            detail: 'Ingrese el número de operación',
            life: 3000
        });
        return;
    }

    if (!userCashRegister.value) {
        toast.add({
            severity: 'error',
            summary: 'Caja No Disponible',
            detail: 'No tienes una caja abierta asignada',
            life: 4000
        });
        return;
    }

    if (!selectedClient.value?.id) {
        toast.add({
            severity: 'error',
            summary: 'Cliente Sin ID',
            detail: 'El cliente no tiene un ID válido. Por favor, vuelva a registrar el cliente.',
            life: 5000
        });
        console.error('❌ ERROR CRÍTICO: Cliente sin ID', selectedClient.value);
        return;
    }

    processingPayment.value = true;

    try {
        // Calcular totales
        const roomSubtotal = getCurrentRoomPrice() * timeAmount.value;
        const productsSubtotal = products.value.reduce((sum, p) => {
            const quantity = parseFloat(p.quantity || p.cantidad || 0);
            const price = parseFloat(p.precio_venta || p.price || 0);
            return sum + (quantity * price);
        }, 0);
        const totalAmount = roomSubtotal + productsSubtotal;

        // OBTENER RATE_TYPE_ID - Buscar en rateTypes cargados
        const getRateTypeId = () => {
            const rateTypeMap: Record<string, string> = {
                'hour': 'HOUR',
                'day': 'DAY', 
                'night': 'NIGHT'
            };
            
            const rateCode = rateTypeMap[selectedRate.value!];
            const rateType = rateTypes.value.find(rt => rt.code === rateCode);
            
            if (!rateType) {
                throw new Error(`No se encontró el rate type para: ${selectedRate.value}`);
            }
            
            return rateType.id;
        };

        // Preparar datos para el booking - ESTRUCTURA CORREGIDA
        const bookingData = {
            room_id: props.roomData?.id,
            customers_id: selectedClient.value.id, // ASEGURAR QUE EXISTA
            rate_type_id: getRateTypeId(),
            currency_id: selectedCurrency.value?.id,
            check_in: new Date().toISOString(),
            total_hours: calculateTotalHours(),
            rate_per_hour: getCurrentRoomPrice(),
            voucher_type: voucherType.value,
            
            // Pagos
            payments: [
                {
                    payment_method_id: selectedPaymentMethod.value.id,
                    amount: totalAmount,
                    cash_register_id: userCashRegister.value.id,
                    operation_number: selectedPaymentMethod.value.requires_reference ? operationNumber.value : null
                }
            ],
            
            // Productos
            consumptions: products.value.map(p => ({
                product_id: p.id,
                quantity: parseFloat(p.quantity || p.cantidad || 0),
                unit_price: parseFloat(p.precio_venta || p.price || 0)
            }))
        };

        console.log('📤 ENVIANDO BOOKING:', JSON.stringify(bookingData, null, 2));

        const response = await axios.post('/bookings', bookingData);

        console.log('✅ RESPUESTA DEL SERVIDOR:', response.data);

        toast.add({
            severity: 'success',
            summary: 'Éxito',
            detail: response.data.message || 'Booking creado correctamente',
            life: 4000
        });

        resetService();
        showStopDialog.value = false;
        
        setTimeout(() => {
            window.location.reload();
        }, 2000);

    } catch (error: any) {
        console.error('❌ ERROR AL CREAR BOOKING:', error);
        console.error('Respuesta del servidor:', error.response?.data);
        
        // Mostrar errores específicos del backend
        if (error.response?.data?.errors) {
            const errors = error.response.data.errors;
            Object.keys(errors).forEach(key => {
                toast.add({
                    severity: 'error',
                    summary: 'Error de Validación',
                    detail: `${key}: ${Array.isArray(errors[key]) ? errors[key][0] : errors[key]}`,
                    life: 5000
                });
            });
        } else if (error.response?.data?.message) {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: error.response.data.message,
                life: 5000
            });
        } else {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: error.message || 'Error al crear el booking. Verifique la conexión.',
                life: 5000
            });
        }
    } finally {
        processingPayment.value = false;
    }
};

const resetService = () => {
    isTimerRunning.value = false;
    if (timerInterval.value) {
        clearInterval(timerInterval.value);
        timerInterval.value = null;
    }
    
    selectedRate.value = null;
    selectedClient.value = null;
    products.value = [];
    timeAmount.value = 1;
    remainingSeconds.value = 0;
    totalSeconds.value = 0;
    voucherType.value = 'boleta';
    
    // Resetear estados de pago
    selectedPaymentMethod.value = null;
    operationNumber.value = '';
};

// Métodos de estado
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

// Cargar datos necesarios
const loadNecessaryData = async () => {
    try {
        // Cargar en paralelo
        const [currenciesRes, paymentMethodsRes, cashRegisterRes, rateTypesRes] = await Promise.all([
            axios.get('/currencies'),
            axios.get('/payments/methods'),
            axios.get('/payments/user-cash-register'),
            axios.get('/rate-types')
        ]);

        currencies.value = currenciesRes.data.data || currenciesRes.data;
        paymentMethods.value = paymentMethodsRes.data.data || paymentMethodsRes.data;
        userCashRegister.value = cashRegisterRes.data.data;
        rateTypes.value = rateTypesRes.data.data || rateTypesRes.data;

        // Seleccionar por defecto
        if (currencies.value.length > 0 && !selectedCurrency.value) {
            selectedCurrency.value = currencies.value[0];
        }
        
        // Seleccionar efectivo por defecto si no hay selección
        if (!selectedPaymentMethod.value) {
            const cashMethod = paymentMethods.value.find(m => m.code === 'cash');
            if (cashMethod) {
                selectedPaymentMethod.value = cashMethod;
            }
        }

    } catch (error: any) {
        console.error('Error al cargar datos:', error);
        
        // Manejar error específico de caja
        if (error.response?.status === 404) {
            throw new Error('No tienes una caja abierta. Debes aperturar una caja primero.');
        } else {
            throw new Error('No se pudieron cargar los datos necesarios');
        }
    }
};

// Cargar datos iniciales
const loadInitialData = async () => {
    try {
        await loadNecessaryData();
    } catch (error: any) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: error.message,
            life: 5000
        });
    }
};

// Watchers
watch([timeAmount, selectedRate], () => {
    if (!isTimerRunning.value && selectedRate.value) {
        remainingSeconds.value = calculateTotalSeconds();
    }
});

// Lifecycle hooks
onMounted(() => {
    loadInitialData();
    
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
.p-invalid {
    border-color: #ef4444;
}

.p-error {
    color: #ef4444;
    font-size: 0.875rem;
    margin-top: 0.25rem;
    display: block;
}
</style>
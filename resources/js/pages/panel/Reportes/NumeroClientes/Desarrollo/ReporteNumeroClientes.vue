<template>
    <div class="space-y-4">
        <!-- HEADER -->
        <div class="flex justify-between items-center">
            <h2 class="text-3xl font-bold">Gestión de Clientes</h2>
            <div class="flex gap-3">
                <Calendar v-model="selectedDate" view="month" dateFormat="mm/yy" placeholder="Seleccionar mes"
                    @date-select="onDateChange" fluid />
            </div>
        </div>

        <!-- TARJETAS DE RESUMEN CON MESSAGES -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <Message severity="info" :closable="false">
                <div class="flex items-start gap-3 w-full">
                    <i class="pi pi-users text-3xl"></i>
                    <div class="flex-1">
                        <h4 class="font-bold text-lg mb-1">Total Clientes</h4>
                        <div class="text-3xl font-black text-blue-700">
                            {{ totales.total_clientes }}
                        </div>
                        <p class="text-xs mt-1 opacity-75">Clientes registrados</p>
                    </div>
                </div>
            </Message>

            <Message severity="warn" :closable="false">
                <div class="flex items-start gap-3 w-full">
                    <i class="pi pi-chart-line text-3xl"></i>
                    <div class="flex-1">
                        <h4 class="font-bold text-lg mb-1">Este Mes</h4>
                        <div class="text-3xl font-black text-orange-700">
                            {{ totales.clientes_este_mes }}
                        </div>
                        <div class="mt-2 flex items-center gap-2">
                            <span :class="[
                                'text-sm font-bold',
                                totales.variacion_porcentual >= 0 ? 'text-green-600' : 'text-red-600'
                            ]">
                                <i
                                    :class="totales.variacion_porcentual >= 0 ? 'pi pi-arrow-up' : 'pi pi-arrow-down'"></i>
                                {{ Math.abs(totales.variacion_porcentual) }}%
                            </span>
                            <span class="text-xs opacity-75">vs mes anterior</span>
                        </div>
                    </div>
                </div>
            </Message>

            <Message severity="success" :closable="false">
                <div class="flex items-start gap-3 w-full">
                    <i class="pi pi-calendar-plus text-3xl"></i>
                    <div class="flex-1">
                        <h4 class="font-bold text-lg mb-1">Promedio Mensual</h4>
                        <div class="text-3xl font-black text-green-700">
                            {{ promedioMensual }}
                        </div>
                        <p class="text-xs mt-1 opacity-75">Clientes por mes</p>
                    </div>
                </div>
            </Message>
        </div>

        <!-- GRÁFICAS -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Gráfica Mensual -->
            <div class="">
                <div class="flex items-center gap-2 mb-4 pb-3 border-b">
                    <i class="pi pi-chart-bar text-blue-600 text-lg"></i>
                    <div>
                        <h3 class="font-bold">Clientes por Mes</h3>
                        <p class="text-xs">Últimos 12 meses</p>
                    </div>
                </div>
                <Chart type="bar" :data="chartDataMensual" :options="chartOptions" class="h-80" />
            </div>

            <!-- Gráfica Diaria -->
            <div class="">
                <div class="flex items-center gap-2 mb-4 pb-3 border-b">
                    <i class="pi pi-chart-line text-teal-600 text-lg"></i>
                    <div>
                        <h3 class="font-bold">Clientes por Día</h3>
                        <p class="text-xs">{{ selectedMonthName }} {{ selectedYear }}</p>
                    </div>
                </div>
                <Chart type="line" :data="chartDataDiarios" :options="chartOptionsLine" class="h-80" />
            </div>
        </div>
        <!-- LOADING -->
        <div v-if="loading" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-xl flex items-center gap-4">
                <i class="pi pi-spin pi-spinner text-blue-500 text-3xl"></i>
                <span class="text-gray-700 font-semibold">Cargando datos...</span>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import Chart from 'primevue/chart';
import Message from 'primevue/message';
import Calendar from 'primevue/calendar';
import Button from 'primevue/button';

// Estados reactivos
const totales = ref({
    total_clientes: 0,
    clientes_activos: 0,
    clientes_inactivos: 0,
    clientes_este_mes: 0,
    clientes_mes_anterior: 0,
    variacion_porcentual: 0
});

const chartDataMensual = ref({
    labels: [],
    datasets: [
        {
            label: 'Clientes Registrados',
            data: [],
            backgroundColor: 'rgba(59, 130, 246, 0.8)',
            borderColor: 'rgba(37, 99, 235, 1)',
            borderWidth: 2,
            borderRadius: 6
        }
    ]
});

const chartDataDiarios = ref({
    labels: [],
    datasets: [
        {
            label: 'Clientes Registrados',
            data: [],
            backgroundColor: 'rgba(20, 184, 166, 0.2)',
            borderColor: 'rgba(13, 148, 136, 1)',
            borderWidth: 3,
            tension: 0.4,
            fill: true,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: '#fff',
            pointBorderWidth: 2
        }
    ]
});

const loading = ref(false);
const error = ref('');

// Fecha seleccionada con Calendar de PrimeVue
const selectedDate = ref(new Date());

const months = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
];

// Computed
const selectedYear = computed(() => selectedDate.value.getFullYear());
const selectedMonth = computed(() => selectedDate.value.getMonth() + 1);

const selectedMonthName = computed(() => {
    return months[selectedDate.value.getMonth()];
});

const tasaActivacion = computed(() => {
    if (totales.value.total_clientes === 0) return 0;
    return Math.round((totales.value.clientes_activos / totales.value.total_clientes) * 100);
});

const promedioMensual = computed(() => {
    const datasets = chartDataMensual.value.datasets[0]?.data || [];
    if (datasets.length === 0) return 0;
    const suma = datasets.reduce((acc: number, val: number) => acc + val, 0);
    return Math.round(suma / datasets.length);
});

const clientesHoy = computed(() => {
    const datasets = chartDataDiarios.value.datasets[0]?.data || [];
    if (datasets.length === 0) return 0;
    return datasets[datasets.length - 1] || 0;
});

// Opciones de gráficas mejoradas
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true,
            position: 'top',
            labels: {
                color: '#374151',
                font: { size: 12, weight: 'bold' },
                padding: 15,
                usePointStyle: true
            }
        },
        tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            titleFont: { size: 13, weight: 'bold' },
            bodyFont: { size: 12 },
            cornerRadius: 8
        }
    },
    scales: {
        x: {
            ticks: {
                color: '#6B7280',
                font: { size: 11 }
            },
            grid: {
                display: false
            }
        },
        y: {
            beginAtZero: true,
            ticks: {
                color: '#6B7280',
                font: { size: 11 },
                stepSize: 1
            },
            grid: {
                color: 'rgba(0, 0, 0, 0.05)'
            }
        }
    }
};

const chartOptionsLine = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true,
            position: 'top',
            labels: {
                color: '#374151',
                font: { size: 12, weight: 'bold' },
                padding: 15,
                usePointStyle: true
            }
        },
        tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            titleFont: { size: 13, weight: 'bold' },
            bodyFont: { size: 12 },
            cornerRadius: 8
        }
    },
    scales: {
        x: {
            ticks: {
                color: '#6B7280',
                font: { size: 11 }
            },
            grid: {
                color: 'rgba(0, 0, 0, 0.05)'
            }
        },
        y: {
            beginAtZero: true,
            ticks: {
                color: '#6B7280',
                font: { size: 11 },
                stepSize: 1
            },
            grid: {
                color: 'rgba(0, 0, 0, 0.05)'
            }
        }
    }
};

// Métodos con Axios
const cargarClientesTotales = async () => {
    try {
        loading.value = true;
        error.value = '';
        const response = await axios.get('/reports/clientes-totales');
        totales.value = response.data;
    } catch (err) {
        console.error('Error cargando clientes totales:', err);
        error.value = 'Error al cargar los datos totales de clientes';
    } finally {
        loading.value = false;
    }
};

const cargarClientesMensual = async () => {
    try {
        error.value = '';
        const response = await axios.get('/reports/clientes-mensual');
        const data = response.data;

        chartDataMensual.value = {
            labels: data.labels,
            datasets: [
                {
                    label: 'Clientes Registrados',
                    data: data.datasets[0].data,
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgba(37, 99, 235, 1)',
                    borderWidth: 2,
                    borderRadius: 6
                }
            ]
        };
    } catch (err) {
        console.error('Error cargando clientes mensual:', err);
        error.value = 'Error al cargar los datos mensuales';
    }
};

const cargarClientesDiarios = async () => {
    try {
        loading.value = true;
        error.value = '';
        const response = await axios.get(`/reports/clientes-diarios/${selectedYear.value}/${selectedMonth.value}`);
        const data = response.data;

        chartDataDiarios.value = {
            labels: data.labels,
            datasets: [
                {
                    label: `Clientes - ${selectedMonthName.value} ${selectedYear.value}`,
                    data: data.datasets[0].data,
                    backgroundColor: 'rgba(20, 184, 166, 0.2)',
                    borderColor: 'rgba(13, 148, 136, 1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#fff',
                    pointBorderWidth: 2
                }
            ]
        };
    } catch (err) {
        console.error('Error cargando clientes diarios:', err);
        error.value = 'Error al cargar los datos diarios';
    } finally {
        loading.value = false;
    }
};

// Manejador de cambio de fecha
const onDateChange = () => {
    cargarClientesDiarios();
};

// Cargar todos los datos iniciales
const cargarTodosLosDatos = async () => {
    loading.value = true;
    try {
        await Promise.all([
            cargarClientesTotales(),
            cargarClientesMensual(),
            cargarClientesDiarios()
        ]);
    } catch (err) {
        console.error('Error cargando datos:', err);
        error.value = 'Error al cargar los datos';
    } finally {
        loading.value = false;
    }
};

// Inicializar
onMounted(() => {
    cargarTodosLosDatos();
});
</script>

<template>
    <div>
        <!-- HEADER -->
        <div class="mb-6">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div>
                    <h2 class="text-3xl font-bold">Ingreso Neto</h2>
                    <p class="mt-1">Utilidad después de deducir todos los egresos</p>
                </div>
                <div class="flex gap-3">
                    <Calendar 
                        v-model="filtroMes" 
                        view="month" 
                        dateFormat="mm/yy" 
                        placeholder="Seleccionar mes"
                        fluid
                        @date-select="cargarTodosDatos"
                    />
                </div>
            </div>
        </div>

        <!-- TARJETA PRINCIPAL INGRESO NETO -->
        <Message :severity="ingresoNetoSeverity" :closable="false" class="mb-6">
            <div class="flex justify-between items-start w-full">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <i class="pi pi-wallet text-3xl"></i>
                        <div>
                            <h3 class="text-2xl font-bold">S/ {{ formatoMoneda(ingresoNeto.ingreso_neto) }}</h3>
                            <p class="text-sm opacity-90">Ingreso Neto Total</p>
                        </div>
                    </div>
                    <div class="flex gap-6 text-sm mt-4">
                        <div>
                            <div class="opacity-75 mb-1">Ingreso Bruto</div>
                            <div class="font-bold">S/ {{ formatoMoneda(ingresoNeto.ingreso_bruto) }}</div>
                        </div>
                        <div>
                            <div class="opacity-75 mb-1">Egresos Totales</div>
                            <div class="font-bold">S/ {{ formatoMoneda(ingresoNeto.egresos_totales) }}</div>
                        </div>
                        <div>
                            <div class="opacity-75 mb-1">Margen</div>
                            <div class="font-bold">{{ ingresoNeto.margen_ganancia }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        </Message>

        <!-- MÉTRICAS PRINCIPALES -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <Message severity="success" :closable="false">
                <div class="flex items-start gap-3">
                    <i class="pi pi-arrow-up text-2xl"></i>
                    <div>
                        <h4 class="font-bold text-lg mb-1">Ingreso Bruto</h4>
                        <div class="text-2xl font-bold text-green-700 mb-2">
                            S/ {{ formatoMoneda(ingresoNeto.ingreso_bruto) }}
                        </div>
                        <div class="text-xs space-y-1">
                            <div>Habitaciones: S/ {{ formatoMoneda(ingresoNeto.ingresos_habitaciones) }}</div>
                            <div>Productos: S/ {{ formatoMoneda(ingresoNeto.ingresos_productos) }}</div>
                        </div>
                    </div>
                </div>
            </Message>

            <Message severity="error" :closable="false">
                <div class="flex items-start gap-3">
                    <i class="pi pi-arrow-down text-2xl"></i>
                    <div>
                        <h4 class="font-bold text-lg mb-1">Egresos Totales</h4>
                        <div class="text-2xl font-bold text-red-700 mb-2">
                            S/ {{ formatoMoneda(ingresoNeto.egresos_totales) }}
                        </div>
                        <div class="text-xs space-y-1">
                            <div>Compras: S/ {{ formatoMoneda(ingresoNeto.egresos_movimientos) }}</div>
                            <div>Personal: S/ {{ formatoMoneda(ingresoNeto.egresos_personal) }}</div>
                        </div>
                    </div>
                </div>
            </Message>

            <Message :severity="margenSeverity" :closable="false">
                <div class="flex items-start gap-3">
                    <i class="pi pi-percentage text-2xl"></i>
                    <div>
                        <h4 class="font-bold text-lg mb-1">Margen Ganancia</h4>
                        <div class="text-2xl font-bold mb-2" :class="margenColorClass">
                            {{ ingresoNeto.margen_ganancia }}%
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                            <div 
                                class="h-2 rounded-full transition-all duration-500" 
                                :class="margenBarraClase"
                                :style="{ width: Math.min(Math.abs(ingresoNeto.margen_ganancia), 100) + '%' }"
                            ></div>
                        </div>
                    </div>
                </div>
            </Message>

            <Message severity="info" :closable="false">
                <div class="flex items-start gap-3">
                    <i class="pi pi-chart-pie text-2xl"></i>
                    <div>
                        <h4 class="font-bold text-lg mb-1">Eficiencia</h4>
                        <div class="text-2xl font-bold text-blue-700 mb-2">
                            {{ ingresoNeto.porcentaje_egresos }}%
                        </div>
                        <div class="text-xs">
                            <div>Por cada S/ 1.00</div>
                            <div class="font-bold">S/ {{ (ingresoNeto.porcentaje_egresos / 100).toFixed(2) }} en gastos</div>
                        </div>
                    </div>
                </div>
            </Message>
        </div>

        <!-- GRÁFICAS -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
            <div class="lg:col-span-2">
                <div class="flex items-center gap-2 mb-4 pb-3 border-b">
                    <i class="pi pi-chart-bar text-blue-600 text-lg"></i>
                    <h3 class="font-boldx">Ingresos vs Egresos por Día</h3>
                </div>
                <Chart 
                    type="bar" 
                    :data="graficaComparativa" 
                    :options="opcionesComparativa"
                    class="h-64"
                />
            </div>

            <div class="">
                <div class="flex items-center gap-2 mb-4 pb-3 border-b">
                    <i class="pi pi-chart-pie text-green-600 text-lg"></i>
                    <h3 class="font-bold">Distribución</h3>
                </div>
                <Chart 
                    type="doughnut" 
                    :data="graficaDistribucion" 
                    :options="opcionesDoughnut"
                    class="h-64"
                />
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
            <div class="">
                <div class="flex items-center gap-2 mb-4 pb-3">
                    <i class="pi pi-chart-line text-purple-600 text-lg"></i>
                    <h3 class="font-bold">Tendencia de Ingreso Neto</h3>
                </div>
                <Chart 
                    type="line" 
                    :data="graficaTendencia" 
                    :options="opcionesLinea"
                    class="h-64"
                />
            </div>

            <div class="">
                <div class="flex items-center gap-2 mb-4 pb-3 border-b">
                    <i class="pi pi-calendar text-orange-600 text-lg"></i>
                    <h3 class="font-bold">Últimos 6 Meses</h3>
                </div>
                <Chart 
                    type="bar" 
                    :data="graficaComparativaMensual" 
                    :options="opcionesComparativaMensual"
                    class="h-64"
                />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

// PrimeVue Components
import Chart from 'primevue/chart';
import Button from 'primevue/button';
import Calendar from 'primevue/calendar';
import Message from 'primevue/message';

// Types
interface IngresoNeto {
    ingreso_neto: number;
    ingreso_bruto: number;
    egresos_totales: number;
    ingresos_habitaciones: number;
    ingresos_productos: number;
    egresos_movimientos: number;
    egresos_personal: number;
    margen_ganancia: number;
    porcentaje_egresos: number;
}

interface DatosGrafica {
    dia: string;
    dia_numero: number;
    ingresos: number;
    egresos: number;
    ingreso_neto: number;
}

interface ComparativaMensual {
    mes: string;
    mes_nombre: string;
    anio: number;
    ingreso_bruto: number;
    egresos_totales: number;
    ingreso_neto: number;
    margen_ganancia: number;
}

interface Distribucion {
    categoria: string;
    valor: number;
    tipo: string;
    color: string;
    icono: string;
}

// Estado reactivo
const cargando = ref(false);
const filtroMes = ref(new Date());
const ingresoNeto = ref<IngresoNeto>({
    ingreso_neto: 0,
    ingreso_bruto: 0,
    egresos_totales: 0,
    ingresos_habitaciones: 0,
    ingresos_productos: 0,
    egresos_movimientos: 0,
    egresos_personal: 0,
    margen_ganancia: 0,
    porcentaje_egresos: 0
});
const datosGrafica = ref<DatosGrafica[]>([]);
const comparativaMensual = ref<ComparativaMensual[]>([]);
const distribucion = ref<Distribucion[]>([]);

// Computed
const resumenPeriodo = computed(() => {
    const fecha = filtroMes.value;
    const year = fecha.getFullYear();
    const month = fecha.getMonth();
    const monthName = fecha.toLocaleDateString('es-ES', { month: 'long' });
    
    return {
        mesAnio: `${monthName.charAt(0).toUpperCase() + monthName.slice(1)} ${year}`
    };
});

const ingresoNetoSeverity = computed(() => {
    return ingresoNeto.value.ingreso_neto >= 0 ? 'success' : 'error';
});

const margenSeverity = computed(() => {
    return ingresoNeto.value.margen_ganancia >= 20 
        ? 'success' 
        : ingresoNeto.value.margen_ganancia >= 10 
        ? 'warn' 
        : 'error';
});

const margenColorClass = computed(() => {
    return ingresoNeto.value.margen_ganancia >= 20 
        ? 'text-green-700' 
        : ingresoNeto.value.margen_ganancia >= 10 
        ? 'text-yellow-700' 
        : 'text-red-700';
});

const margenBarraClase = computed(() => {
    return ingresoNeto.value.margen_ganancia >= 20 
        ? 'bg-green-600' 
        : ingresoNeto.value.margen_ganancia >= 10 
        ? 'bg-yellow-600' 
        : 'bg-red-600';
});

const indicadores = computed(() => {
    const margen = ingresoNeto.value.margen_ganancia;
    
    return {
        rentabilidad: margen >= 25 ? 'Excelente' : margen >= 15 ? 'Buena' : margen >= 5 ? 'Regular' : 'Baja',
        eficiencia: ingresoNeto.value.porcentaje_egresos <= 60 ? 'Alta' : ingresoNeto.value.porcentaje_egresos <= 80 ? 'Media' : 'Baja',
        sostenibilidad: margen >= 10 ? 'Sostenible' : 'En Riesgo',
        crecimiento: margen >= 20 ? 'Alto' : margen >= 10 ? 'Moderado' : 'Limitado'
    };
});

const getSeverityIndicador = (tipo: string): string => {
    const valor = indicadores.value[tipo as keyof typeof indicadores.value];
    
    if (['Excelente', 'Alta', 'Sostenible', 'Alto'].includes(valor)) return 'success';
    if (['Buena', 'Moderado'].includes(valor)) return 'info';
    if (['Regular', 'Media'].includes(valor)) return 'warn';
    return 'error';
};

const formatoMoneda = (valor: number) => {
    return new Intl.NumberFormat('es-PE', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(valor);
};

const obtenerParametrosMes = () => {
    const fecha = filtroMes.value;
    return {
        month: fecha.getMonth() + 1,
        year: fecha.getFullYear()
    };
};

const cargarTodosDatos = async () => {
    cargando.value = true;
    try {
        await Promise.all([
            cargarIngresoNeto(),
            cargarIngresoNetoGrafica(),
            cargarIngresoNetoComparativa(),
            cargarIngresoNetoDistribucion()
        ]);
    } catch (error) {
        console.error('Error cargando datos:', error);
    } finally {
        cargando.value = false;
    }
};

const cargarIngresoNeto = async () => {
    try {
        const params = obtenerParametrosMes();
        const response = await axios.get('/reports/ingreso-neto', { params });
        ingresoNeto.value = response.data.data;
    } catch (error) {
        console.error('Error cargando ingreso neto:', error);
    }
};

const cargarIngresoNetoGrafica = async () => {
    try {
        const params = obtenerParametrosMes();
        const response = await axios.get('/reports/ingreso-neto-grafica', { params });
        datosGrafica.value = response.data.data;
    } catch (error) {
        console.error('Error cargando gráfica:', error);
        datosGrafica.value = [];
    }
};

const cargarIngresoNetoComparativa = async () => {
    try {
        const response = await axios.get('/reports/ingreso-neto-comparativa');
        comparativaMensual.value = response.data.data;
    } catch (error) {
        console.error('Error cargando comparativa:', error);
        comparativaMensual.value = [];
    }
};

const cargarIngresoNetoDistribucion = async () => {
    try {
        const params = obtenerParametrosMes();
        const response = await axios.get('/reports/ingreso-neto-distribucion', { params });
        distribucion.value = response.data.data;
    } catch (error) {
        console.error('Error cargando distribución:', error);
        distribucion.value = [];
    }
};

const graficaComparativa = computed(() => {
    if (!datosGrafica.value.length) {
        return {
            labels: ['No hay datos'],
            datasets: [
                { label: 'Ingresos', data: [0], backgroundColor: '#10B981' },
                { label: 'Egresos', data: [0], backgroundColor: '#EF4444' }
            ]
        };
    }

    return {
        labels: datosGrafica.value.map(item => `Día ${item.dia_numero}`),
        datasets: [
            {
                label: 'Ingresos',
                data: datosGrafica.value.map(item => item.ingresos),
                backgroundColor: '#10B981',
                borderColor: '#059669',
                borderWidth: 1
            },
            {
                label: 'Egresos',
                data: datosGrafica.value.map(item => item.egresos),
                backgroundColor: '#EF4444',
                borderColor: '#DC2626',
                borderWidth: 1
            }
        ]
    };
});

const graficaDistribucion = computed(() => {
    if (!distribucion.value.length) {
        return {
            labels: ['No hay datos'],
            datasets: [{ data: [1], backgroundColor: ['#e0e0e0'] }]
        };
    }

    return {
        labels: distribucion.value.map(item => item.categoria),
        datasets: [{
            data: distribucion.value.map(item => item.valor),
            backgroundColor: distribucion.value.map(item => item.color),
            borderWidth: 2,
            borderColor: '#fff'
        }]
    };
});

const graficaTendencia = computed(() => {
    if (!datosGrafica.value.length) {
        return {
            labels: ['No hay datos'],
            datasets: [{ label: 'Ingreso Neto', data: [0], borderColor: '#3B82F6', backgroundColor: 'rgba(59, 130, 246, 0.1)' }]
        };
    }

    return {
        labels: datosGrafica.value.map(item => `Día ${item.dia_numero}`),
        datasets: [{
            label: 'Ingreso Neto',
            data: datosGrafica.value.map(item => item.ingreso_neto),
            borderColor: ingresoNeto.value.ingreso_neto >= 0 ? '#10B981' : '#EF4444',
            backgroundColor: ingresoNeto.value.ingreso_neto >= 0 ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    };
});

const graficaComparativaMensual = computed(() => {
    if (!comparativaMensual.value.length) {
        return {
            labels: ['No hay datos'],
            datasets: [{ label: 'Ingreso Neto', data: [0], backgroundColor: '#3B82F6' }]
        };
    }

    return {
        labels: comparativaMensual.value.map(item => `${item.mes_nombre} ${item.anio}`),
        datasets: [{
            label: 'Ingreso Neto',
            data: comparativaMensual.value.map(item => item.ingreso_neto),
            backgroundColor: comparativaMensual.value.map(item => 
                item.ingreso_neto >= 0 ? '#10B981' : '#EF4444'
            ),
            borderColor: comparativaMensual.value.map(item => 
                item.ingreso_neto >= 0 ? '#059669' : '#DC2626'
            ),
            borderWidth: 1
        }]
    };
});

const opcionesComparativa = {
    maintainAspectRatio: false,
    responsive: true,
    plugins: {
        legend: {
            labels: {
                color: '#374151',
                font: { size: 11 }
            }
        }
    },
    scales: {
        x: {
            ticks: {
                color: '#6B7280',
                font: { size: 10 }
            },
            grid: {
                color: '#F3F4F6'
            }
        },
        y: {
            ticks: {
                color: '#6B7280',
                font: { size: 10 },
                callback: function(value: any) {
                    return 'S/ ' + value;
                }
            },
            grid: {
                color: '#F3F4F6'
            }
        }
    }
};

const opcionesDoughnut = {
    maintainAspectRatio: false,
    responsive: true,
    plugins: {
        legend: {
            position: 'bottom',
            labels: {
                color: '#374151',
                font: { size: 10 },
                padding: 8
            }
        }
    }
};

const opcionesLinea = {
    maintainAspectRatio: false,
    responsive: true,
    plugins: {
        legend: {
            labels: {
                color: '#374151',
                font: { size: 11 }
            }
        }
    },
    scales: {
        x: {
            ticks: {
                color: '#6B7280',
                font: { size: 10 }
            },
            grid: {
                color: '#F3F4F6'
            }
        },
        y: {
            ticks: {
                color: '#6B7280',
                font: { size: 10 },
                callback: function(value: any) {
                    return 'S/ ' + value;
                }
            },
            grid: {
                color: '#F3F4F6'
            }
        }
    }
};

const opcionesComparativaMensual = {
    maintainAspectRatio: false,
    responsive: true,
    plugins: {
        legend: {
            labels: {
                color: '#374151',
                font: { size: 11 }
            }
        }
    },
    scales: {
        x: {
            ticks: {
                color: '#6B7280',
                font: { size: 10 }
            },
            grid: {
                color: '#F3F4F6'
            }
        },
        y: {
            ticks: {
                color: '#6B7280',
                font: { size: 10 },
                callback: function(value: any) {
                    return 'S/ ' + value;
                }
            },
            grid: {
                color: '#F3F4F6'
            }
        }
    }
};

onMounted(() => {
    cargarTodosDatos();
});
</script>

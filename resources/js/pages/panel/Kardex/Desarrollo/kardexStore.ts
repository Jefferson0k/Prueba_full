import { defineStore } from 'pinia';
import axios from 'axios';

export const useKardexStore = defineStore('kardex', {
  state: () => ({
    kardexData: [],
    productoNombre: '',
    loading: false,
    pagination: {
      currentPage: 1,
      lastPage: 1,
      perPage: 15,
      total: 0
    }
  }),
  
  actions: {
    async fetchKardex(params) {
      this.loading = true;
      try {
        const response = await axios.get('/kardex', { params });
        
        this.kardexData = response.data.data;
        
        // Guardar el nombre del producto del primer registro
        if (this.kardexData.length > 0) {
          this.productoNombre = this.kardexData[0].product_nombre;
        }
        
        // Guardar información de paginación
        if (response.data.meta) {
          this.pagination = {
            currentPage: response.data.meta.current_page,
            lastPage: response.data.meta.last_page,
            perPage: response.data.meta.per_page,
            total: response.data.meta.total
          };
        }
        
        console.log('Kardex cargado:', this.kardexData.length, 'registros');
      } catch (error) {
        console.error('Error al cargar kardex:', error);
        this.kardexData = [];
        this.productoNombre = '';
      } finally {
        this.loading = false;
      }
    },
    
    clearKardex() {
      this.kardexData = [];
      this.productoNombre = '';
    }
  }
});
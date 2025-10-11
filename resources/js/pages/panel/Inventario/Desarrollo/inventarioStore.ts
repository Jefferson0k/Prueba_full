import { defineStore } from 'pinia';
import axios from 'axios';

export const useInventarioStore = defineStore('inventario', {
  state: () => ({
    inventarioData: [],
    resumen: null,
    loading: false,
    pagination: {
      currentPage: 1,
      lastPage: 1,
      perPage: 15,
      total: 0,
      from: 0,
      to: 0
    }
  }),
  
  actions: {
    async fetchInventario(subBranchId: string, params = {}, page = 1) {
      this.loading = true;
      try {
        const requestParams = {
          ...params,
          page: page,
          per_page: this.pagination.perPage
        };

        const response = await axios.get(`/kardex/sub-branches/${subBranchId}/inventario`, { 
          params: requestParams 
        });
        
        this.inventarioData = response.data.data;
        
        // Guardar el resumen del backend
        if (response.data.resumen) {
          this.resumen = response.data.resumen;
        }
        
        // Guardar información de paginación
        if (response.data.pagination) {
          this.pagination = {
            currentPage: response.data.pagination.current_page,
            lastPage: response.data.pagination.last_page,
            perPage: response.data.pagination.per_page,
            total: response.data.pagination.total,
            from: response.data.pagination.from || 0,
            to: response.data.pagination.to || 0
          };
        }
        
        console.log('Inventario cargado:', this.inventarioData.length, 'productos de', this.pagination.total);
        console.log('Resumen:', this.resumen);
      } catch (error) {
        console.error('Error al cargar inventario:', error);
        this.inventarioData = [];
        this.resumen = null;
      } finally {
        this.loading = false;
      }
    },
    
    clearInventario() {
      this.inventarioData = [];
      this.resumen = null;
      this.pagination = {
        currentPage: 1,
        lastPage: 1,
        perPage: 15,
        total: 0,
        from: 0,
        to: 0
      };
    }
  }
});
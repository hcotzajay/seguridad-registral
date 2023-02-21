<template>
  <v-container>
    <v-card
        elevation="12"
        class="mx-auto my-12"
        max-width="1500"
    >
      <h2 class="tituloReporte">Reporte consultas realizadas por rango de fecha</h2>
      <v-form ref="consultaRangoFecha" v-model="buscarRangoFecha" v-on:submit.prevent="buscarRangoFecha">
        <v-container>
          <v-row>
            <v-col
                cols="12"
                sm="5"
            >
              <v-menu
                  v-model="menu2"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                      label="Fecha de inicio"
                      prepend-icon="mdi-calendar"
                      readonly
                      v-bind="attrs"
                      v-model="dateFormatted"
                      @blur="date = parseDate(dateFormatted)"
                      v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                    v-model="date"
                    :max="date2"
                    no-title
                    @input="menu2 = false"
                ></v-date-picker>
              </v-menu>
            </v-col>
            <v-col
                cols="12"
                sm="5"
            >
              <v-menu
                  v-model="menu3"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                      label="Fecha de fin"
                      prepend-icon="mdi-calendar"
                      readonly
                      v-bind="attrs"
                      v-model="dateFormattedFechaFinal"
                      @blur="date2 = parseDateFinal(dateFormattedFechaFinal)"
                      v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                    v-model="date2"
                    :min="date"
                    no-title
                    @input="menu3 = false"
                ></v-date-picker>
              </v-menu>
            </v-col>
            <v-col
                cols="12"
                sm="2"
            >
              <v-icon
                  color="red"
                  class="ma-2 pa-2"
                  x-large
                  @click="generarReporte"
              >
                mdi-file-pdf-box-outline
              </v-icon>
            </v-col>
          </v-row>
        </v-container>

        <v-card-actions class="pt-0">
          <align-actions>
            <cancel-btn
                @click="limpiar"
            >
              Limpiar
              <v-icon
                  right
                  dark
              >
                mdi-broom
              </v-icon>
            </cancel-btn>

            <v-btn
                rounded
                color="primary"
                :loading="loading"
                :disabled="loading"
                @click="loader = 'loading'"
            >
              Buscar
              <v-icon
                  right
                  dark
              >
                mdi-account-search
              </v-icon>
            </v-btn>
          </align-actions>
        </v-card-actions>
      </v-form>

      <!--      <v-container>-->
      <!--        <div class="d-flex flex-row-reverse mb-6 bg-surface-variant">-->
      <!--          <v-icon-->
      <!--              color="red"-->
      <!--              class="ma-2 pa-2"-->
      <!--              x-large-->
      <!--              @click="generarReporte"-->
      <!--          >-->
      <!--            mdi-file-pdf-box-outline-->
      <!--          </v-icon>-->
      <!--        </div>-->
      <!--      </v-container>-->

      <div v-if="existeInformacion">

        <v-data-table
            :headers="headers"
            :items="desserts"
            :page.sync="page"
            :items-per-page="10"
            hide-default-footer
            class="elevation-1"
            @page-count="pageCount = $event"
        >
          <template v-slot:item.accion="{ item }">
            <v-tooltip top color="#171718">
              <template v-slot:activator="{ on, attrs }" v-if="item.result == 'Sí'">
                <v-icon
                    class="mr-2"
                    @click="verResultado(item)"
                    color="#515151"
                    dark
                    v-bind="attrs"
                    v-on="on"
                >
                  mdi-book-edit-outline
                </v-icon>
              </template>
              <span>Ver Resultado</span>
            </v-tooltip>
          </template>

        </v-data-table>
        <div class="text-center pt-2">
          <v-pagination
              v-model="page"
              :length="pageCount"
          ></v-pagination>
        </div>
      </div>
    </v-card>

    <respuesta-consulta v-if="existeRespuestaConsulta" :enviarRespuestaConsulta="enviarRespuestaConsulta, enviarFecha, enviarHora" @cerrar="cerrar"></respuesta-consulta>
  </v-container>
</template>

<script>
import respuestaConsulta from "./respuestaConsulta";

export default {
  name: "consultaFecha",
  components: {respuestaConsulta},
  data: vm => ({
    buscarRangoFecha: false,
    loader: null,
    loading: false,
    date: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
    date2: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
    dateFormatted: vm.formatDate((new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10)),
    dateFormattedFechaFinal: vm.formatDate((new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10)),
    modal: false,
    menu2: false,
    menu3: false,
    existeInformacion: false,
    existeRespuestaConsulta: false,

    page: 1,
    pageCount: 0,
    itemsPerPage: 10,
    headers: [
      {text: 'Fecha de consulta', value: 'fecha_busqueda'},
      {text: 'Tipo de búsqueda', value: 'id_tipo'},
      {text: 'IP', value: 'ip'},
      {text: 'Nombre del colaborador', value: 'nombre_colaborador'},
      {text: 'Obtuvo resultado', value: 'result'},
      {text: 'Acción', value: 'accion'},
    ],
    desserts: [],
    enviarRespuestaConsulta: [],
    enviarFecha: [],
    enviarHora: []

  }),
  computed: {
    computedDateFormattedInicial() {
      return this.formatDate(this.date)
    },
    computedDateFormattedFinal() {
      return this.formatDateFinal(this.date2)
    },
  },
  watch: {
    date(val) {
      this.dateFormatted = this.formatDate(this.date)
    },
    date2(val) {
      this.dateFormattedFechaFinal = this.formatDate(this.date2)
    },
    loader() {
      const l = this.loader
      this[l] = !this[l]
      if (this.loader) {
        this.buscarFecha()
      }
    },
  },
  methods: {
    verResultado(item) {
      axios.get('/consultarDatosRespuesta', {
        params: {
          idResultado: item.id_datos
        }
      })
          .then(res => {
            this.loading = false
            this.enviarRespuestaConsulta = res.data.datos
            this.enviarFecha = res.data.fecha
            this.enviarHora = res.data.hora
            this.existeRespuestaConsulta = true
          })
          .catch(err => {
            this.$iziToast.fail(err, this.$Progress)
            this.loading = false
          })
          .finally(() => {
            this.loading = false
            this.cancelarLoading()
          })

    },
    cerrar() {
      this.existeRespuestaConsulta = false
    },
    buscarFecha() {
      axios.get('/consultarRangoFechas', {
        params: {
          fechaInicio: this.date,
          fechaFin: this.date2
        }
      })
          .then(res => {
            this.loading = false
            this.desserts = res.data.resultados
            this.existeInformacion = true
          })
          .catch(err => {
            this.$iziToast.fail(err, this.$Progress)
            this.loading = false
          })
          .finally(() => {
            this.loading = false
            this.cancelarLoading()
          })
    },
    cancelarLoading() {
      const l = this.loader
      this[l] = false
      this.loader = null
    },
    formatDate(date) {
      if (!date) return null
      const [year, month, day] = date.split('-')
      return `${day}/${month}/${year}`
    },
    formatDateFinal(date2) {
      if (!date2) return null
      const [year, month, day] = date2.split('-')
      return `${day}/${month}/${year}`
    },
    parseDate(date) {
      if (!date) return null
      const [day, month, year] = date.split('/')
      return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`
    },
    parseDateFinal(date2) {
      if (!date2) return null
      const [day, month, year] = date2.split('/')
      return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`
    },
    limpiar() {
      this.date = ''
      this.date2 = ''
      this.cancelarLoading()
    },
    generarReporte() {
      if (this.desserts.length > 0) {
        let datos = {
          datosObtenidos: this.desserts,
          fechaInicial: this.date,
          fechaFinal: this.date2
        }

        let myJson = JSON.stringify(datos)
        window.open('/generarReporteRangoFechas?json=' + myJson)
      } else {
        this.$iziToast.warning('Alerta', 'No existen datos para generar el reporte.')
        return false
      }
    },
  },
}
</script>

<style scoped>
.tituloReporte {
  text-align: center;
}
</style>
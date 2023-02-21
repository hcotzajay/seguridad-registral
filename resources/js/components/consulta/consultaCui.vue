<template>
  <v-container>
    <cancel-btn
        class="ma-2"
        color="success"
        dark
        @click="regresar"
    >
      <v-icon
          dark
          right
      >
        mdi-arrow-left
      </v-icon>
      &nbsp; Regresar
    </cancel-btn>
    <v-card
        class="mx-auto"
        max-width="800"
    >
      <v-container>
        <v-form ref="busquedaCui" v-model="buscarCui" v-on:submit.prevent="buscarCui">
          <v-row>
            <v-col>
              <v-text-field
                  v-model="cui"
                  counter="13"
                  maxlength="13"
                  label="Ingresar Código Único de Identificación CUI"
                  required
                  autocomplete="off"
                  :rules="validarCUIRule"
                  onkeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;"
                  @keyup.enter="loader = 'loading'"
              ></v-text-field>
            </v-col>
          </v-row>
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
      </v-container>

      <v-container>
        <v-alert
            border="top"
            colored-border
            type="warning"
            elevation="2"
            v-if="mensaje!==''"
        >
          {{ mensaje }}
        </v-alert>
      </v-container>
    </v-card>

    <v-container>
      <v-row
          align="center"
          justify="center"
      >
        <v-col cols="auto">
          <v-icon
              x-large
              @click="generarReporte"
          >
            mdi-file-pdf
          </v-icon>
        </v-col>
      </v-row>
    </v-container>


    <respuesta v-if="respuestaObtenida" :enviarRespuesta="enviarRespuesta"></respuesta>

  </v-container>
</template>

<script>
import respuesta from "./respuesta";

export default {
  name: "consultaCui",
  components: {respuesta},

  data: () => ({
    buscarCui: false,
    cui: '',
    existeNoHit: false,
    loader: null,
    loading: false,
    mensaje: '',
    /*datos: [{
      CUI: '3030486460108',
      PRIMER_NOMBRE: 'Henry',
      SEGUNDO_NOMBRE: 'Leonel',
      TERCER_NOMBRE: null,
      PRIMER_APELLIDO: 'Cotzajay',
      SEGUNDO_APELLIDO: 'Canel',
      FECHA_NACIMIENTO: '17/11/1996',
      GENERO: 'M',
      ESTADO_CIVIL: 'S',
      NACIONALIDAD: 'GUATEMALA',
      FECHA_DEFUNCION: null,
      OCUPACION: 'ESTUDIANTE',
      VECINDAD: 'GUATEMALA, MIXCO'
    }],
    datosPruebaReporte: {
      "result": true,
      "fecha": "15/12/2022",
      "responseCode": 200,
      "hora": "14:01:46",
      "mensaje": "Se muestran los resultados encontrados.",
      "data": [
        {
          "CUI": "1729730820701",
          "PRIMER_NOMBRE": "Juan",
          "SEGUNDO_NOMBRE": "Pedro",
          "TERCER_NOMBRE": null,
          "PRIMER_APELLIDO": "De León",
          "SEGUNDO_APELLIDO": "Moro",
          "FECHA_NACIMIENTO": "25/06/1989",
          "GENERO": "M",
          "ESTADO_CIVIL": "S",
          "NACIONALIDAD": "GUATEMALA",
          "FECHA_DEFUNCION": null,
          "OCUPACION": "ESTUDIANTE",
          "VECINDAD": "GUATEMALA, GUATEMALA"
        }
      ]
    },*/

    respuestaCompletaObtenida: [],
    respuestaObtenida: false,
    enviarRespuesta: [],
    dataCui: [],
    validarCUIRule: [
      v => !!v || 'El Código Único de Identificación es requerido.',
      v => v.length == 13 || 'Debe tener 13 caracteres.',
      v => v > isNaN(v) || 'El valor ingresado debe ser mayor a 0',
      v => v.length >= 0 || 'Debe ingresar un valor válido',
    ],
  }),
  methods: {
    regresar() {
      this.limpiar()
      this.$emit('regresar', null)
    },
    limpiar() {
      this.cui = ''
      this.$refs.busquedaCui.resetValidation()
      this.dataCui = []
    },
    buscarInfoCui() {
      /*this.respuestaObtenida = true
      this.enviarRespuesta = this.datos
      this.cancelarLoading()*/
      if (this.cui === '' || this.cui.length < 13) {
        this.$iziToast.warning('Alerta', 'Debe completar los campos para poder continuar.')
        this.cancelarLoading()
      } else {
        axios.post('/consultaCui', {
          params: {
            cui: this.cui,
            tipoConsulta: 1
          }
        })
            .then(res => {
              this.$iziToast.msg(res, this.$Progress)
              this.respuestaObtenida = true
              this.enviarRespuesta = res.data['consulta']['data']
              this.respuestaCompletaObtenida = res.data
              this.mensaje = res.data['message']
              this.loading = false
            })
            .catch(err => {
              this.$iziToast.fail(err, this.$Progress)
              this.loading = false
            })
            .finally(() => {
              this.loading = false
              this.cancelarLoading()
            })
      }
    },
    cancelarLoading() {
      const l = this.loader
      this[l] = false
      this.loader = null
    },
    generarReporte() {
      if (this.cui.length === 13) {
        let datosPersona = {
          cui: this.cui,
          datosObtenidos: this.respuestaCompletaObtenida
        }
        let myJson = JSON.stringify(datosPersona)
        window.open('/generarReporteCui?json=' + myJson, '_blank')
      } else {
        this.$iziToast.warning('Alerta', 'Debe completar algún campo para poder generar el reporte.')
        return false
      }
    },
  },
  watch: {
    loader() {
      const l = this.loader
      this[l] = !this[l]
      if (this.loader) {
        this.buscarInfoCui()
      }
    },
  },
}
</script>

<style scoped>
.centrar {
  display: flex;
  justify-content: center;
}

.not-allowed {
  cursor: not-allowed;
}

.event-none {
  pointer-events: none;
}
</style>
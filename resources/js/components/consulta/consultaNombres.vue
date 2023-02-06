<template>
  <v-container>
    <cancel-btn
        class="ma-2"
        color="success"
        dark
        @click="regresarNombres"
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
        max-width="1300"
    >
      <v-container>
        <v-form ref="nombres" v-model="busquedaNombres" v-on:submit.prevent="busquedaNombres">
          <v-row>
            <v-col>
              <v-text-field
                  v-model="primerNombre"
                  counter="20"
                  maxlength="20"
                  label="Ingrese primer nombre"
                  required
                  autocomplete="off"
                  :rules="validarPrimerNombreRule"
                  @keyup.enter="loader = 'loading'"
              ></v-text-field>
            </v-col>

            <v-col>
              <v-text-field
                  v-model="segundoNombre"
                  counter="20"
                  maxlength="20"
                  label="Ingrese segundo nombre"
                  autocomplete="off"
                  @keyup.enter="loader = 'loading'"
              ></v-text-field>
            </v-col>
          </v-row>
          <v-row>
            <v-col>
              <v-text-field
                  v-model="primerApellido"
                  counter="20"
                  maxlength="20"
                  label="Ingrese primer apellido"
                  autocomplete="off"
                  :rules="validarPrimerApellidoRule"
                  @keyup.enter="loader = 'loading'"
              ></v-text-field>
            </v-col>
            <v-col>
              <v-text-field
                  v-model="segundoApellido"
                  counter="20"
                  maxlength="20"
                  label="Ingrese segundo apellido"
                  autocomplete="off"
                  @keyup.enter="loader = 'loading'"
              ></v-text-field>
            </v-col>
            <v-col>
              <v-text-field
                  v-model="fechaNacimiento"
                  counter="10"
                  maxlength="10"
                  label="Fecha de nacimiento  dd/mm/YY"
                  autocomplete="off"
                  :rules="validarFechaRule"
                  onkeypress="if (event.keyCode < 46 || event.keyCode > 57) event.returnValue = false;"
                  @keyup.enter="loader = 'loading'"
                  clearable
                  @click:clear="limpiarFecha"
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
    </v-card>

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
  name: "consultaNombres",
  components: {respuesta},


  data: () => ({
    busquedaNombres: false,
    primerNombre: '',
    segundoNombre: '',
    primerApellido: '',
    segundoApellido: '',
    date: '',
    modal: false,
    menu2: false,
    respuestaObtenida: false,
    loader: null,
    loading: false,
    enviarRespuesta: [],
    limpiarRespuesta: [],
    mensaje: '',
    fechaNacimiento: '',
    respuestaCompletaObtenida: [],
    // dataNombres:[],
    /*    dataNombres: [{
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
        }],*/

    /*datosPruebaReporte: {
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

    // Reglas del formulario
    validarPrimerNombreRule: [
      v => !!v || 'El primer nombre es requerido.',
    ],
    validarPrimerApellidoRule: [
      v => !!v || 'El Apellido es requerido.',
    ],
    validarFechaRule: [
      v => !!v || 'La fecha de nacimiento es requerida.',
      v => /^(?:3[01]|[12][0-9]|0?[1-9])([\-/.])(0?[1-9]|1[1-2])\1\d{4}$/.test(v.toUpperCase()) || 'No cumple el parámetro de número de Documento'
    ],
  }),
  methods: {
    regresarNombres() {
      this.$emit('regresarNombres', null)
    },
    limpiar() {
      this.primerNombre = ''
      this.segundoNombre = ''
      this.primerApellido = ''
      this.segundoApellido = ''
      this.date = ''
      this.$refs.nombres.resetValidation()
    },
    buscarInfoNombres() {
      // this.enviarRespuesta = this.dataNombres
      // this.respuestaObtenida = true
      // this.cancelarLoading()
      let respuesta = this.$refs.nombres.validate()
      if (respuesta === false) {
        this.$iziToast.warning('Alerta', 'Debe completar los campos para poder continuar.')
        this.cancelarLoading()
        return false
      }
      this.respuestaObtenida = true
      if (this.primerNombre === '' || this.primerApellido === '' || this.fechaNacimiento === '') {
        this.$iziToast.warning('Alerta', 'Debe completar los campos para poder continuar.')
        this.cancelarLoading()
      } else {
        axios.post('/consultaNombres', {
          params: {
            primerNombre: this.primerNombre,
            segundoNombre: this.segundoNombre,
            primerApellido: this.primerApellido,
            segundoApellido: this.segundoApellido,
            fechaNacimiento: this.fechaNacimiento,
            tipoConsulta: 2
          }
        })
            .then(res => {
              this.respuestaObtenida = true
              this.enviarRespuesta = res.data['consulta']['data']
              this.respuestaCompletaObtenida = res.data
              this.$iziToast.msg(res, this.$Progress)
              this.mensaje = res.data['message']
              this.loading = false
            })
            .catch(err => {
              this.$iziToast.fail(err, this.$Progress)
              this.loading = false
            })
            .finally(() => {
              this.loading = false
            })
      }
    },
    cancelarLoading() {
      const l = this.loader
      this[l] = false
      this.loader = null
    },
    validarCaracter() {
      let ingresoDia = false
      let ingresoMes = false
      let ingresoAni = false

      if (this.fechaNacimiento.length === 2) {
        this.fechaNacimiento = this.fechaNacimiento + '/'
        ingresoDia = true
      }

      if (this.fechaNacimiento.length === 5) {
        this.fechaNacimiento = this.fechaNacimiento + '/'
        ingresoMes = true
      }
    },
    limpiarFecha() {
      this.fechaNacimiento = ''
    },
    generarReporte() {
      let respuesta = this.$refs.nombres.validate()
      if (respuesta === false) {
        this.$iziToast.warning('Alerta', 'Debe completar los campos para poder continuar.')
        this.cancelarLoading()
        return false
      }


      let datosPersona = {
        primerNombre: this.primerNombre,
        segundoNombre: this.segundoNombre,
        primerApellido: this.primerApellido,
        segundoApellido: this.segundoApellido,
        fechaNacimiento: this.fechaNacimiento,
        datosObtenidos: this.respuestaCompletaObtenida
      }
      let myJson = JSON.stringify(datosPersona)
      window.open('/generarReporteNombres?json=' + myJson, '_blank')
    },
  },
  watch: {
    loader() {
      const l = this.loader
      this[l] = !this[l]
      if (this.loader) {
        this.buscarInfoNombres()
      }
    }
  },
}
</script>

<style scoped>
.centrar {
  display: flex;
  justify-content: center;
}
</style>
<template>
  <v-container style="margin-top: -0%">
    <div v-for="(item) in enviarRespuesta">
      <v-card
          style="margin-top: 4%"
          class="mx-auto"
          max-width="1500"
      >
        <v-alert
            color="cyan"
            border="left"
            elevation="2"
            colored-border
            icon="mdi-account-tie"
        >
          <h5><b>Datos de la persona</b></h5>
          <v-container>
            <v-row>
              <v-col
                  class="not-allowed"
              >
                <v-text-field
                    v-model="item.CUI"
                    label="Cui"
                    class="event-none"
                    readonly
                ></v-text-field>
              </v-col>
              <v-col
                  class="not-allowed"
              >
                <v-text-field
                    v-model="item.PRIMER_NOMBRE"
                    label="Primer Nombre"
                    readonly
                    class="event-none"
                ></v-text-field>
              </v-col>
              <v-col
                  class="not-allowed"
              >
                <v-text-field
                    v-model="item.SEGUNDO_NOMBRE"
                    label="Segundo Nombre"
                    readonly
                    class="event-none"
                ></v-text-field>
              </v-col>
              <v-col
                  class="not-allowed"
              >
                <v-text-field
                    v-model="item.TERCER_NOMBRE === null ? 'NULL' : item.TERCER_NOMBRE"
                    label="Tercer Nombre"
                    readonly
                    class="event-none"
                ></v-text-field>
              </v-col>
            </v-row>

            <v-row>
              <v-col
                  class="not-allowed"
              >
                <v-text-field
                    v-model="item.PRIMER_APELLIDO"
                    label="Primer Apellido"
                    readonly
                    class="event-none"
                ></v-text-field>
              </v-col>
              <v-col
                  class="not-allowed"
              >
                <v-text-field
                    v-model="item.SEGUNDO_APELLIDO === null ? 'NULL' : item.SEGUNDO_APELLIDO"
                    label="Segundo apellido"
                    readonly
                    class="event-none"
                ></v-text-field>
              </v-col>
              <v-col
                  class="not-allowed"
              >
                <v-text-field
                    v-model="item.FECHA_NACIMIENTO"
                    label="Fecha de nacimiento"
                    readonly
                    class="event-none"
                ></v-text-field>
              </v-col>
              <v-col
                  class="not-allowed"
              >
                <v-text-field
                    v-model="item.GENERO"
                    label="Genero"
                    readonly
                    class="event-none"
                ></v-text-field>
              </v-col>
              <v-col
                  class="not-allowed"
              >
                <v-text-field
                    v-model="item.ESTADO_CIVIL"
                    label="Estado Civil"
                    readonly
                    class="event-none"
                ></v-text-field>
              </v-col>
            </v-row>

            <v-row>
              <v-col
                  class="not-allowed"
              >
                <v-text-field
                    v-model="item.NACIONALIDAD"
                    label="Nacionalidad"
                    readonly
                    class="event-none"
                ></v-text-field>
              </v-col>
              <v-col
                  class="not-allowed"
              >
                <v-text-field
                    v-model="item.FECHA_DEFUNCION === null ? 'NULL' : item.FECHA_DEFUNCION"
                    label="Fecha defunción"
                    readonly
                    class="event-none"
                ></v-text-field>
              </v-col>
              <v-col
                  class="not-allowed"
              >
                <v-text-field
                    v-model="item.OCUPACION"
                    label="Ocupación"
                    readonly
                    class="event-none"
                ></v-text-field>
              </v-col>
              <v-col
                  class="not-allowed"
              >
                <v-text-field
                    v-model="item.VECINDAD"
                    label="Vecindad"
                    readonly
                    class="event-none"
                ></v-text-field>
              </v-col>
            </v-row>
          </v-container>
        </v-alert>
      </v-card>
    </div>
  </v-container>
</template>

<script>
export default {
  name: "respuesta",
  data: () => ({
    /*dataNombres: [{
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
  }),
  methods: {},
  props: {
    enviarRespuesta: [],
  }
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
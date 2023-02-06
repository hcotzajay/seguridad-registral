<template>
    <v-container>

        <v-row>
            <v-col
                cols="1"
                sm="6"
            >
                <v-text-field
                    label="Primer Nombre"
                    placeholder="Placeholder"
                    outlined
                    clearable
                ></v-text-field>
            </v-col>
            <v-col
                cols="2"
                sm="6"
            >
                <v-text-field
                    label="Segundo Nombre"
                    placeholder="Placeholder"
                    outlined
                    clearable
                ></v-text-field>
            </v-col>
        </v-row>
        <v-row>
            <v-col
                cols="1"
                sm="6"
            >
                <v-text-field
                    label="Primer Apellido"
                    placeholder="Placeholder"
                    outlined
                    clearable
                ></v-text-field>
            </v-col>
            <v-col
                cols="2"
                sm="6"
            >
                <v-text-field
                    id="SApellido"
                    label="Segundo Apellido"
                    placeholder="Placeholder"
                    outlined
                    clearable
                ></v-text-field>
            </v-col>
        </v-row>
        <v-row>
            <v-col
                cols="2"
                sm="6"
            >
                <div>
                    <v-menu
                        ref="menu"
                        v-model="menu"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                        min-width="auto"
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                                v-model="date"
                                label="Fecha de Nacimiento"
                                placeholder="Fecha de Nacimiento"

                                prepend-icon="mdi-calendar"
                                readonly
                                v-bind="attrs"
                                v-on="on"

                                outlined
                                clearable

                            ></v-text-field>
                        </template>
                        <v-date-picker
                            v-model="date"
                            :active-picker.sync="activePicker"
                            :max="(new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10)"
                            min="1950-01-01"
                            @change="save"
                        ></v-date-picker>
                    </v-menu>
                </div>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import Vuetify from "vuetify";

export default {
    name: "consulta_nombres",
    vuetify: new Vuetify(),
    data: () => ({
        activePicker: null,
        date: null,
        menu: false,
    }),
    watch: {
        menu(val) {
            val && setTimeout(() => (this.activePicker = 'YEAR'))
        },
    },
    methods: {
        save(date) {
            this.$refs.menu.save(date)

        },
    },
}
</script>

<style scoped>

</style>

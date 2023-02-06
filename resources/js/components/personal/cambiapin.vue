<template>
    <v-container>
        <h1>Cambio de PIN</h1>

        <v-card
            class="mx-auto"
            max-width="600"
            outlined
            color=""
        >
            <v-alert
                icon="mdi-shield-lock-outline"
                dense
                outlined
                prominent
                text
                type="warning"
            >
                <ul>
                    <li class="pb-3">
                        Por las políticas de seguridad, se requiere que el PIN cumpla con:
                        <ul>
                            <li>Tener exáctamente <strong>4</strong> dígitos.</li>
                        </ul>
                    </li>
                </ul>
            </v-alert>
            <v-card-title>
                <span class="">Por favor ingrese los siguientes datos:</span>
            </v-card-title>
            <v-card-text>
                <v-container>
                    <v-form ref="form" v-model="valid">
                        <v-row>
                            <v-col cols="12">
                                <text-field-password
                                    label="Contraseña"
                                    hint="La mísma que utilizas para ingresar al sistema."
                                    persistent-hint
                                    v-model="form.password"
                                    @keyup.enter="setPIN"
                                ></text-field-password>
                            </v-col>
                            <v-col cols="6">
                                <text-field-pin
                                    label="Nuevo PIN"
                                    v-model="form.pin"
                                    @keyup.enter="setPIN"
                                ></text-field-pin>
                            </v-col>
                            <v-col cols="6">
                                <text-field-pin
                                    label="Confirme su nuevo PIN"
                                    v-model="form.confirmPin"
                                    @keyup.enter="setPIN"
                                ></text-field-pin>
                            </v-col>
                        </v-row>
                    </v-form>
                </v-container>
                <small>*Todos los campos son obligatorios</small>
            </v-card-text>
            <align-actions
                class="mb-3"
            >
                <action-btn
                    :loading="loading"
                    @click="setPIN"
                >
                    Cambiar
                </action-btn>
            </align-actions>
        </v-card>
    </v-container>
</template>

<script>
    import md5 from "crypto-js/md5"
    import TextFieldPin from "../base/Text-fields/text-field-pin";
    import TextFieldPassword from "../base/Text-fields/text-field-password";

    export default {
        name: "cambiapin",

        components: {TextFieldPassword, TextFieldPin},

        data: () => ({
            valid: false,
            form: {password: '', pin: '', confirmPin: ''},
            defaultForm: {password: '', pin: '', confirmPin: ''},
            loading: false,
        }),

        methods: {
            setPIN() {
                if (!this.$refs.form.validate()) {
                    return
                }

                if (this.form.pin !== this.form.confirmPin) {
                    this.$iziToast.error({title: 'Error en PIN', message: 'Los 4 dígitos ingresados no coinciden.'})
                    return
                }
                this.$Progress.start();
                this.loading = true

                axios.post('/personal/cambiaPIN', {
                    password: window.btoa(md5(this.form.password)),
                    pin: window.btoa(md5(this.form.pin))
                })
                    .then(res => {
                        this.$iziToast.msg(res, this.$Progress);
                        this.form = Object.assign({}, this.defaultForm)
                    })
                    .catch(err => {
                        this.$iziToast.fail(err, this.$Progress)
                    })
                    .finally(() => {
                        this.loading = false
                        this.$refs.form.resetValidation()
                    })
            },
        },
    }
</script>

<style scoped>

</style>

<template>
    <v-app id="inspire" :style="envColor">
        <v-main>
            <v-container fluid>
                <v-row align="center" justify="center" class="text-center ma-0">
                    <v-card class="elevation-0 d-flex justify-center" color="transparent">
                        <img class="logo-login" :src="path_logo" alt="logo-login">
                    </v-card>
                </v-row>
                <v-row align="center" justify="center">
                    <h1>{{ appName }}</h1>
                </v-row>
                <v-row align="center" justify="center">
                    <v-col cols="12" sm="8" md="4">
                        <v-card class="elevation-7" color="grey lighten-5">
                            <v-toolbar dark color="primary" class="elevation-7 mx-11" style="width: auto;">
                                <v-flex class="text-center">
                                    <v-toolbar-title>Ingreso al sistema</v-toolbar-title>
                                </v-flex>
                            </v-toolbar>
                            <v-card-text>
                                <v-form ref="form" v-model="valid" class="mx-9">
                                    <v-text-field prepend-icon="mdi-account" name="usuario" label="Usuario" v-model="form.usuario" type="text"
                                                  :rules="[rules.required, rules.spaces]" @keyup.enter="login"
                                    ></v-text-field>
                                    <v-text-field prepend-icon="mdi-lock" name="password" label="Contraseña" id="password" v-model="form.password" type="password"
                                                  :rules="[rules.required, rules.spaces]" @keyup.enter="login"
                                    ></v-text-field>
                                </v-form>
                            </v-card-text>
                            <v-card-actions>
                                <v-flex class="text-center">
                                    <v-btn rounded outlined center :loading="btnLogin" color="primary" @click="login" :disabled="!valid">
                                        Iniciar sesión
                                    </v-btn>
                                </v-flex>
                            </v-card-actions>
                            <v-flex class="ma-1 py-2">
                                <v-btn block text color="secondary" small @click.stop="reveal = true">
                                    Recuperar contraseña
                                </v-btn>
                            </v-flex>
                            <password-reset :reveal="reveal" @cancel_reset="cancel_reset"/>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>
        <the-footer/>
    </v-app>
</template>

<script>
    import 'typeface-great-vibes'
    import md5 from 'crypto-js/md5'
    import PasswordReset from "./login/password-reset";
    import TheFooter from "./work-area/the-footer";

    export default {
        name: "login",

        components: {TheFooter, PasswordReset},

        props: {
            appName: {
                type: String,
                required: true,
            },
            nameServerAplicaciones: {
                type: String,
                required: true,
            },
            appEnvColor: {
                type: String,
                required: true,
            },
            logo: {
                type: String,
                required: true,
            }
        },

        created() {
            this.$helper.getVariablesENV(this.$props)
        },

        data() {
            return {
                valid: false,
                rules: {
                    required: v => !!v || 'Requerido.',
                    spaces: v => (v || '').indexOf(' ') < 0 || 'No se permiten espacios',
                    digits: v => /^([0-9])*$/.test(v) || 'Solo se permiten dígitos',
                },
                form: {usuario: '', password: ''},
                btnLogin: false,
                path_logo: this.logo,
                disable: false,
                reveal: false,
            }
        },

        computed: {
            envColor() {
                const background = 'background-color: #'
                if (this.appEnvColor.trim()) {
                    return background + `${this.appEnvColor.trim()}`
                }
                return background + 'F5F5F5'
            }
        },

        methods: {
            login() {
                if (!this.$refs.form.validate()) {
                    return
                }

                this.btnLogin = true
                this.form.password = md5(this.form.password) + ''

                axios.post('/login', this.form)
                    .then(res => {
                        localStorage.setItem('user', JSON.stringify(res.data.datos.usuario))
                        if (this.$router.currentRoute.path != '/')
                            this.$router.push('/')
                        this.$refs.form.resetValidation()
                        location.reload()
                    })
                    .catch(err => {
                        this.$iziToast.fail(err, this.$Progress)
                    })
                    .finally(() => {
                        this.form.password = ''
                        this.btnLogin = false
                    })
            },

            cancel_reset() {
                this.reveal = false
            }
        }
    }
</script>

<style scoped>
    h1 {
        font-family: 'Great Vibes';
        font-size: calc(2em + 2vw);
        font-weight: normal;
    }

    .logo-login {
        max-width: 100%;
        max-height: 250px;
    }
</style>

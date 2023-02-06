<template>
    <v-app id="inspire" :style="envColor">
        <v-app-bar dark color="primary" flat app>
            <!--<v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>-->
            <v-layout column>
                <v-toolbar-title class="text-h5">
                    <span class="hidden-xs-only">{{ appName }} ></span>
                    {{ modulo }}
                </v-toolbar-title>
            </v-layout>
            <v-spacer></v-spacer>
            <v-layout align-end column pr-3 class="hidden-xs-only">
                <span class="subtitle-1">{{ dataUser.nombre }}</span>
                <span class="caption">{{ dataUser.unidad }}</span>
                <span class="caption hidden-sm-only">{{ dataUser.puesto }}</span>
            </v-layout>
            <v-avatar>
                <img :src="dataUser.foto" alt="fotografia">
            </v-avatar>
            <v-tooltip bottom color="tooltip">
                <template v-slot:activator="{ on, attrs }">
                    <v-btn icon text @click="logout" v-bind="attrs" v-on="on">
                        <v-icon>mdi-logout-variant</v-icon>
                    </v-btn>
                </template>
                <span>Cerrar sesión</span>
            </v-tooltip>

            <clock/>

        </v-app-bar>
        <v-navigation-drawer class="grey lighten-3" :mini-variant.sync="mini" v-model="drawer" floating permanent app>
            <v-list>
                <v-list-item :class="mini && 'px-0'">
                    <v-list-item class="px-1 justify-center">
                        <v-list-item-avatar :size="mini ? '48px' : '128px'" class="mt-0 mb-3 mx-0">
                            <img :src="path_logo" alt="escudoRGP">
                        </v-list-item-avatar>

                        <v-list-item-title :style="mini ? '' : 'display: none;'"></v-list-item-title>

                        <v-btn icon @click.stop="mini = !mini">
                            <v-icon>mdi-chevron-left</v-icon>
                        </v-btn>
                    </v-list-item>
                </v-list-item>
                <v-divider></v-divider>
                <v-list-group
                    v-for="item in items"
                    :key="item.nombre"
                    v-model="item.active"
                    :prepend-icon="item.icon"
                    no-action
                >
                    <template v-slot:activator>
                        <v-list-item-content>
                            <v-list-item-title>{{ item.nombre }}</v-list-item-title>
                        </v-list-item-content>
                    </template>

                    <v-list-item
                        class="pl-10"
                        v-for="(subItem, i) in item.opciones"
                        :to="{name: subItem.nombre.trim()}"
                        :key="subItem.nombre"
                        v-model="subItem.active"
                        color="primary"
                        @click="updateEncabezado(item)"
                        link
                    >
                        <v-tooltip right color="tooltip">
                            <template v-slot:activator="{ on, attrs }">
                                <v-list-item-icon
                                    class="mr-5"
                                    v-bind="attrs"
                                    v-on="on"
                                >
                                    <v-icon>{{ subItem.icon }}</v-icon>
                                </v-list-item-icon>
                                <v-list-item-title
                                    v-bind="attrs"
                                    v-on="on"
                                >{{ subItem.nombre }}
                                </v-list-item-title>
                            </template>
                            <span>{{ subItem.descripcion }}</span>
                        </v-tooltip>
                        <!--<v-list-item-title v-text="subItem.title"></v-list-item-title>-->
                    </v-list-item>

                    <v-divider v-model="item.active"></v-divider>

                </v-list-group>
            </v-list>
        </v-navigation-drawer>
        <v-main>
            <v-container fluid pt-0>
                <v-flex text-xs-center>
                    <router-view></router-view>
                </v-flex>
            </v-container>
        </v-main>
        <the-footer></the-footer>
    </v-app>
</template>

<script>
    import TheFooter from "./work-area/the-footer";
    import Clock from "./work-area/clock";

    export default {
        name: "work-area",

        components: {Clock, TheFooter},

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
        data() {
            return {
                dataUser: JSON.parse(localStorage.user),
                mini: true,
                drawer: null,
                path_logo: this.logo,
                modulo: localStorage.modulo,
                items: [],
                icons: require('./data/icons.json'),
            }
        },
        created() {
            this.$helper.getVariablesENV(this.$props)
            this.doMenu()
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
            doMenu() {
                this.$Progress.start()
                axios.get('/menu')
                    .then(res => {
                        let menu_y_rutas = this.transformMenu(res.data.menu.sistema.modulos[0].modulo)
                        this.items = menu_y_rutas.menu
                        localStorage.setItem('user', JSON.stringify(res.data.datos.usuario))
                        this.$Progress.finish()
                    })
                    .catch(err => {
                        this.$iziToast.fail(err, this.$Progress)
                    })
            },
            loadComponent(component) {
                return () => import(`../components/${component}.vue`)
                    .catch(err => this.$iziToast.importComponent(component, this.$Progress))
            },
            transformMenu(menu) {
                let newMenu = {}
                Object.keys(menu).forEach((key) => {
                    let modulo = menu[key]
                    let tmp = {}
                    try {
                        tmp.nombre = modulo.nombre
                        tmp.icon = this.getIconOfMenu(tmp.nombre)
                        tmp.opciones = []
                        // Recorrido de las opciones del módulo
                        Object.keys(modulo.opciones[0].opcion).forEach((key) => {
                            const opcion = modulo.opciones[0].opcion[key]
                            let path = opcion.$
                            try {
                                const elementos_del_path = path.split('/')
                                const _modulo = elementos_del_path[elementos_del_path.length - 2]
                                const _opcion = elementos_del_path[elementos_del_path.length - 1]
                                let path_del_componente = _modulo + '/' + _opcion

                                opcion.icon = this.getIconOfMenu(opcion.nombre.trim().split(' ').join('-'))
                                tmp.opciones.push(opcion)
                                let ruta = {}

                                switch (path) {
                                    case '/seguridad/?m=login&o=cambiapin':
                                        path_del_componente = 'personal/cambiapin'
                                        ruta = {
                                            path: '/' + path_del_componente,
                                            name: opcion.nombre.trim(),
                                            component: this.loadComponent(path_del_componente)
                                        }
                                        break;
                                    case '/seguridad/?m=login&o=cambiapass':
                                        path_del_componente = 'personal/cambiapass'
                                        ruta = {
                                            path: '/' + path_del_componente,
                                            name: opcion.nombre.trim(),
                                            component: this.loadComponent(path_del_componente)
                                        }
                                        break;
                                    default:
                                        ruta = {
                                            path: path,//opcion.nombre.trim().split(' ').join('-'),
                                            name: opcion.nombre.trim(),
                                            component: this.loadComponent(path_del_componente)
                                        }
                                }
                                this.$router.addRoute(ruta)
                            } catch (err) {
                                console.log('Opción con formato de path distinto ->', opcion);
                            }
                        })
                        newMenu[key] = tmp
                    } catch (err) {
                        console.log('Error en procesar el módulo del ménu ->',);
                    }
                })
                return {menu: newMenu}
            },
            getIconOfMenu(modulo) {
                try {
                    return this.icons.filter((obj) => {
                        return obj.name.toLowerCase() == modulo.toLowerCase()
                    })[0].value
                } catch (err) {
                    return 'mdi-face'
                }
            },
            logout() {
                this.$Progress.start()
                axios.get('/logout')
                    .then(res => {
                        this.$iziToast.msg(res, this.$Progress)
                        localStorage.removeItem('user')
                        localStorage.removeItem('modulo')
                    })
                    .catch(err => {
                        this.$iziToast.fail(err, this.$Progress)
                    })
                    .finally(() => {
                        this.$router.push('/').catch(() => {
                        })
                        location.reload()
                    })
            },
            updateEncabezado(modulo) {

                if (modulo.nombre === 'Personal')
                    return

                localStorage.setItem('modulo', modulo.nombre)
                this.modulo = localStorage.modulo
            },
        }
    }
</script>

<style scoped>

</style>

<template>
    <v-tooltip color="grey darken-4" top>
        <template v-slot:activator="{ on }">
            <v-icon
                class="mr-2"
                color="grey darken-4"
                v-on="on"
                @click="onFocus"
            >
                mdi-cloud-download-outline
            </v-icon>
        </template>
        <span>Descargar</span>
    </v-tooltip>
</template>

<script>
    export default {
        name: 'DownloadBtn',

        methods: {
            onFocus() {
                this.$emit('clicked')
            },
        }
    }
</script>

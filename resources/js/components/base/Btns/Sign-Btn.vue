<template>
    <v-tooltip color="grey darken-4" top>
        <template v-slot:activator="{ on }">
            <v-icon
                class="mr-2"
                color="grey darken-4"
                v-on="on"
                @click="onFocus"
            >
                mdi-pen-lock
            </v-icon>
        </template>
        <span>Firmar</span>
    </v-tooltip>
</template>

<script>
    export default {
        name: 'SignBtn',

        methods: {
            onFocus() {
                this.$emit('clicked')
            },
        }
    }
</script>

<template>
    <v-tooltip color="orange darken-4" top>
        <template v-slot:activator="{ on }">
            <v-icon
                class="mr-2"
                color="orange"
                v-on="on"
                @click="onFocus"
            >
                mdi-pencil
            </v-icon>
        </template>
        <span>Editar</span>
    </v-tooltip>
</template>

<script>
    export default {
        name: 'EditBtn',

        methods: {
            onFocus() {
                this.$emit('clicked')
            },
        }
    }
</script>

<template>
    <v-text-field
        type="password"
        prepend-icon="mdi-lock"
        counter
        class="input-group--focused"
        minlength="8"
        required
        v-bind="$attrs"
        v-on="$listeners"
        :rules="[rulesPass.required, rulesPass.spaces, rulesPass.min]"
    >
        <slot/>
    </v-text-field>
</template>

<script>
    export default {
        name: "text-field-password",

        data: () => ({
            rulesPass: {
                required: v => !!v || 'Obligatorio.',
                spaces: v => (v || '').indexOf(' ') < 0 || 'No se permiten espacios',
                min: v => !!v && (v.length >= 8) || 'Debe tener mínimo 8 caracteres',
            },
        }),
    }
</script>

<style scoped>

</style>

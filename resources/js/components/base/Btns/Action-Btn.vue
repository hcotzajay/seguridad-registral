<template>
    <v-btn
        color="primary"
        rounded
        dark
        class="px-5"
        v-bind="$attrs"
        v-on="$listeners"
    >
        <slot/>
    </v-btn>
</template>

<script>
    export default {
        name: 'ActionBtn',
    }
</script>

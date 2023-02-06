<template>
    <v-btn
        color="grey"
        rounded
        outlined
        class="px-5 mr-7"
        v-bind="$attrs"
        v-on="$listeners"
    >
        <slot/>
    </v-btn>
</template>

<script>
    export default {
        name: 'CancelBtn',
    }
</script>

<template>
    <div v-if="number">
        <i class="fa fa-phone"></i> <a :href="`tel:${number}`" v-on:click="onCall(number, $event)">{{ number }}</a>
        <i v-if="enable_tel_presence && number.lastIndexOf('0', 0) != 0"
           class="extStatus" :class="`ext${number} ${number_class}`" :title="number_title"></i>
    </div>
</template>
<script>
    export default {
        props: ['number', 'status'],
        computed: {
            enable_tel_presence(){
                return window.opnucConfig.enable_tel_presence;
            },
            number_title: function () {
                return window.extStatus[this.status]['statusText']
            },
            number_class: function () {
                return window.extStatus[this.status]['statusClass']
            },
        },
        methods: {
            onCall(number, event) {
                this.$events.$emit('Click2Call', number)
            },
        },
    }
</script>

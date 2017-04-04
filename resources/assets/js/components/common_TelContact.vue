<template>
   <div v-if="number">
       <i class="fa fa-phone"></i> <a :href="`tel:${number}`" v-on:click="onCall(number, $event)">{{ number }}</a>
       <i v-if="number.lastIndexOf('0', 0) != 0"
          class="extStatus" :class="`ext${number} ${number_class}`" :title="number_title"></i>
   </div>
</template>
<script>
    export default {
        props: ['number', 'status'],
        computed: {
            number_title: function () {
                return window.extStatus[this.status]['statusText']
            },
            number_class: function () {
                return window.extStatus[this.status]['statusClass']
            },
        },
        methods:{
            onCall(number, event) {

                // OpnUC上で処理する場合、ブラウザ側のイベント処理を無効にする
                event.preventDefault();

                this.$events.$emit('Click2Call', number)
            },
        },
    }
</script>

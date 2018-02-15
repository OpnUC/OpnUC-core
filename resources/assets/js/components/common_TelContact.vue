<template>
    <div v-if="number">
        <i class="fa fa-phone"></i>
        <a :href="`tel:${number}`" v-on:click="onCall(number, $event)">{{ formatPhoneNumber(number) }}</a>

        <i v-if="enableTelPresence && isPhoneNumberExt(number)"
           class="extStatus" :class="`ext${number} ${presenceClass}`" :title="presenceStatus"></i>
    </div>
</template>
<script>
    import PhoneNumber from './common_PhoneNumber'

    export default {
        mixins: [PhoneNumber],
        props: ['number', 'status'],
        computed: {
            /**
             * プレゼンス情報を表示するか
             * @returns {boolean}
             */
            enableTelPresence() {
                return window.opnucConfig.enable_tel_presence;
            },
            presenceStatus: function () {
                return window.extStatus[this.status]['statusText']
            },
            presenceClass: function () {
                return window.extStatus[this.status]['statusClass']
            },
        },
        methods: {
            /**
             * Click2Callのイベントを発火する
             * @param {string} number 電話番号
             * @param event イベント
             */
            onCall(number, event) {
                this.$events.$emit('Click2Call', number)
            },
        },
    }
</script>

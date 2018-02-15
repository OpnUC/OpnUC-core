<script>
    /**
     * 電話番号関係の共通処理
     */
    import {parse, format, AsYouType} from 'libphonenumber-js'

    export default {
        methods: {
            /**
             * 電話番号をフォーマットする
             * @todo ロケール
             * @returns {string}
             */
            formatPhoneNumber: function (value) {
                return !this.isPhoneNumberExt(value) ? new AsYouType('JP').input(value) : value
            },
            /**
             * 電話番号が0で始まっていない事を確認(内線)
             * @returns {boolean}
             */
            isPhoneNumberExt(value){
                return value.lastIndexOf('0', 0) != 0
            },
        },
    }
</script>

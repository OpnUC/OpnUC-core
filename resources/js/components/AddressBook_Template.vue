<template>
    <router-view></router-view>
</template>
<script>
    import Vue from 'vue'

    export default {
        data() {
            return {
                addressBookType: {
                    1: '内線電話帳',
                    2: '共通電話帳',
                    //9 : '個人電話帳',
                },
                addressBookGroup: [],
            }
        },
        created(){
            var _this = this

            axios.get('/addressbook/groupList')
                .then(function (response) {
                    _this.addressBookGroup = response.data

                    _this.$events.$emit('AddressBook:updateGroup', _this.addressBookGroup)
                })
                .catch(function (error) {
                    console.log(error);
                });
        }
    }
</script>
<style>
    /* 内線プレゼンス */
    i.extStatus::after {
        padding-left: 3px;
        font-size: 90%;
        color: #333;
        content: attr(title);
    }
</style>

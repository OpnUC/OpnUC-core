<template>
    <div>
        <div v-if="rowData.position">
            <small>{{ rowData.position }}</small>
        </div>
        <a v-on:click="showDetail" :title="rowData.name_kana">{{ rowData.name }}</a>
    </div>
</template>
<script>
    export default {
        props: {
            rowData: {
                type: Object,
                required: true
            },
        },
        methods:{
            // 詳細の表示
            showDetail() {
                this.$events.$emit('AddressBook:showDetail', this.rowData.id)
            }
        },
    }
</script>
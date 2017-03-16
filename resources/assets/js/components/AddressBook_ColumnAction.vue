<template>
    <div>
        <button type="button" class="btn btn-default btn-xs">
            <i class="fa fa-edit"></i> 編集
        </button>
        <button type="button" class="btn btn-default btn-xs" v-on:click.prevent="onDelete">
            <i class="fa fa-times"></i> 削除
        </button>
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
            onDelete() {
                this.$events.$emit('AddressBook:delete', this.rowData)
            },
        },
    }
</script>
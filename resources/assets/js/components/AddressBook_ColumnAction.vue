<template>
    <div>
        <router-link v-if="$auth.check('addressbook-admin')" :to="{ name: 'AddressBookEdit', params: { id: rowData.id }}" class="btn btn-default btn-xs">
            <i class="fa fa-edit"></i> 編集
        </router-link>
        <button v-if="$auth.check('addressbook-admin')" type="button" class="btn btn-default btn-xs" v-on:click.prevent="onDelete">
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
            // 削除
            onDelete() {
                this.$events.$emit('AddressBook:delete', this.rowData)
            },
        },
    }
</script>
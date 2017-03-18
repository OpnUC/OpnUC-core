<template>
    <ul class="treeview-menu" v-if="item.Child">
        <li v-for="childItem in item.Child">
            <router-link :to="{ name: 'AddressBook', query: { typeId: index, groupId: childItem.Id }}">
                {{ childItem.Name }}
                <i class="fa fa-angle-left pull-right" v-if="childItem.Child"></i>
            </router-link>
            <group-list :item="childItem" :index="index" :keyword="keyword" :parent_groupName="item.Name"></group-list>
        </li>
    </ul>
</template>

<script>
    import Vue from 'vue'

    export default {
        data(){
            return {
                parent_groupName_: this.parent_groupName ? this.parent_groupName + ' > ' + this.item.Name + ' > ' : this.item.Name + ' > '
            }
        },
        props: ['item', 'index', 'keyword', 'parent_groupName'],
        methods: {
            // 検索
            onSearch () {
                this.$events.$emit('AddressBook:search', this.keyword)
            },
            // グループの選択
            onSelect(typeId, groupId, groupName, flag){
                if (!flag) {
                    this.$events.$emit('AddressBook:search', this.keyword, typeId, groupId, groupName)
                }
            },
        },
    }
</script>

<template>
    <ul class="treeview-menu" v-if="item.Child">
        <li class="treeview" v-for="childItem in item.Child">
            <a href="#" v-if="childItem.Child">
                {{ childItem.Name }}
                <i class="fa fa-angle-left pull-right" v-if="childItem.Child"></i>
            </a>
            <router-link v-else :to="{ name: 'AddressBook', query: { typeId: typeId, groupId: childItem.Id }}">
                {{ childItem.Name }}
                <i class="fa fa-angle-left pull-right" v-if="childItem.Child"></i>
            </router-link>
            <group-list :item="childItem" :typeId="typeId"></group-list>
        </li>
    </ul>
</template>

<script>
    export default {
        props: ['item', 'typeId'],
    }
</script>

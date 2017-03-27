<template>
    <div>
        <div v-if="rowData.tel1">
            <i class="fa fa-phone"></i> <a :href="`tel:${rowData.tel1}`">{{ rowData.tel1 }}</a>
            <i v-if="rowData.tel1.lastIndexOf('0', 0) != 0"
               class="fa fa-circle text-gray extStatus" :class="`ext${rowData.tel1}`" :title="rowData.tel1_status"></i>
        </div>
        <div v-if="rowData.tel2">
            <i class="fa fa-phone"></i> <a :href="`tel:${rowData.tel2}`">{{ rowData.tel2 }}</a>
            <i v-if="rowData.tel2.lastIndexOf('0', 0) != 0"
               class="fa fa-circle text-gray extStatus" :class="`ext${rowData.tel2}`" :title="rowData.tel2_status"></i>
        </div>
        <div v-if="rowData.tel3">
            <i class="fa fa-phone"></i> <a :href="`tel:${rowData.tel3}`">{{ rowData.tel3 }}</a>
            <i v-if="rowData.tel3.lastIndexOf('0', 0) != 0"
                class="fa fa-circle text-gray extStatus" :class="`ext${rowData.tel3}`" :title="rowData.tel3_status"></i>
        </div>
        <div v-if="rowData.email">
            <i class="fa fa-envelope"></i> <a :href="`mailto:${rowData.email}`">{{ rowData.email }}</a>
        </div>
    </div>
</template>
<script>
    export default {
        props: {
            rowData: {
                type: Object,
                required: true
            }
        }
    }
</script>
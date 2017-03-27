<template>
    <div>
        <div v-if="rowData.tel1">
            <i class="fa fa-phone"></i> <a :href="`tel:${rowData.tel1}`">{{ rowData.tel1 }}</a>
            <i v-if="rowData.tel1.lastIndexOf('0', 0) != 0"
               class="extStatus" :class="`ext${rowData.tel1} ${tel1_class}`" :title="tel1_title"></i>
        </div>
        <div v-if="rowData.tel2">
            <i class="fa fa-phone"></i> <a :href="`tel:${rowData.tel2}`">{{ rowData.tel2 }}</a>
            <i v-if="rowData.tel2.lastIndexOf('0', 0) != 0"
               class="extStatus" :class="`ext${rowData.tel2} ${tel2_class}`" :title="tel2_title"></i>
        </div>
        <div v-if="rowData.tel3">
            <i class="fa fa-phone"></i> <a :href="`tel:${rowData.tel3}`">{{ rowData.tel3 }}</a>
            <i v-if="rowData.tel3.lastIndexOf('0', 0) != 0"
                class="extStatus" :class="`ext${rowData.tel3} ${tel3_class}`" :title="tel3_title"></i>
        </div>
        <div v-if="rowData.email">
            <i class="fa fa-envelope"></i> <a :href="`mailto:${rowData.email}`">{{ rowData.email }}</a>
        </div>
    </div>
</template>
<script>
    export default {
        computed: {
            tel1_title: function () {
                return window.extStatus[this.rowData.tel1_status]['statusText']
            },
            tel1_class: function () {
                return window.extStatus[this.rowData.tel1_status]['statusClass']
            },
            tel2_title: function () {
                return window.extStatus[this.rowData.tel2_status]['statusText']
            },
            tel2_class: function () {
                return window.extStatus[this.rowData.tel2_status]['statusClass']
            },
            tel3_title: function () {
                return window.extStatus[this.rowData.tel3_status]['statusText']
            },
            tel3_class: function () {
                return window.extStatus[this.rowData.tel3_status]['statusClass']
            },
        },
        props: {
            rowData: {
                type: Object,
                required: true
            }
        }
    }
</script>
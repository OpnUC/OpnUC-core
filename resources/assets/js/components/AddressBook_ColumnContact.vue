<template>
    <div>
        <tel-contact :number="rowData.tel1" :status="rowData.tel1_status">
        </tel-contact>
        <tel-contact :number="rowData.tel2" :status="rowData.tel2_status">
        </tel-contact>
        <tel-contact :number="rowData.tel3" :status="rowData.tel3_status">
        </tel-contact>
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
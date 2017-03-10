<template>
    <div>
        <button type="button" class="btn btn-default btn-xs">
            <i class="fa fa-edit"></i> 編集
        </button>
        <button type="button" class="btn btn-default btn-xs">
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
    }
</script>
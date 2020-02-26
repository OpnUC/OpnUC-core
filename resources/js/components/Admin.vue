<template>
    <section class="content">
        左のメニューから操作を選択してください。
    </section>
</template>

<script>
    import Vue from 'vue'

    export default {
        created() {
            this.$root.sidebar = this.$route.matched.some(record => record.components.sidebar);
        },
    }
</script>

<template>
    <section class="content">
        <div class="error-page">
            <h2 class="headline text-red">404 </h2>
            <div class="error-content">
                <h3><i class="fa fa-warning text-red"></i> Not Found</h3>
                <p>
                    要求されたページが見つかりませんでした。
                </p>
            </div>
        </div>
    </section>
</template>
<script>
    export default {
        created() {
            // サイドバーなし
            this.$root.sidebar = false;
        }
    }
</script>
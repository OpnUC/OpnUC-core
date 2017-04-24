<template>
    <div>
        <span v-for="role in rowData.roles">
            <span class="badge bg-aqua">{{roles[role]}}</span>
        </span>
    </div>
</template>
<script>
    import Vue from 'vue'

    export default {
        data() {
            return {
                roles: [],
            }
        },
        props: {
            rowData: {
                type: Object,
                required: true
            },
        },
        created() {
            var _this = this

            axios.get('/admin/roles')
                .then(function (response) {
                    $.each(response.data, function (index, val) {
                        _this.roles[val.name] = val.display_name
                    });
                    // ゴミをセットして更新させる
                    // ToDo 見直し必要
                    Vue.set(_this.roles, [])
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
    }
</script>
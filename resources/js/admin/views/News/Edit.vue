<template>
    <v-container fill-height fluid grid-list-xl>
        <v-layout justify-center wrap>
            <v-flex md12>
                <NewsForm :title="$t('Common.editNews')"
                             :news-key="$route.params.slug"/>
            </v-flex>
        </v-layout>
    </v-container>

</template>
<script>
import newsApi from "@/api/resources/news";
import NewsForm from "@/components/forms/NewsForm";

export default {
    components: {
        NewsForm
    },
    data() {
        return {
            loading: false,
            slug: ''
        }
    },
    methods: {
        get() {
            this.loading = true;
            newsApi.get(this.$route.params.slug).then(res => {
                this.article = res.data.data;
                this.loading = false;
            }).catch(err => {
                this.$toastr.e('Something went wrong! ' + err);
                this.loading = false;
            })
        },
        update() {
            this.loading = true;

            this.form._method = 'PUT';
            newsApi.update(this.form).then(res => {
                this.$toastr.s('Data update successful');
                this.loading = false;
            }).catch(err => {
                this.$toastr.e('Something went wrong! ' + err);
                this.loading = false;
            })
        }
    },
    created() {
        this.get();
    }
}
</script>
<style>
.ck-editor__editable {
    min-height: 300px !important;
}
</style>

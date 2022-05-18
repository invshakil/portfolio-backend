<template>
    <div>
        <material-card
            :color="color"
            :title="title"
            :text="subTitle"
        >
            <v-simple-table>
                <template v-slot:default>
                    <thead>
                    <tr>
                        <th v-for="header in headers" :class="[header.class || '']" :key="header.key">
                            {{ header.key.toUpperCase() }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(article, index) in articles.slice(0,10)" :key="index">
                        <td> {{ article.title }}</td>
                        <td> {{ article.hit_count }}</td>
                    </tr>
                    </tbody>
                </template>
            </v-simple-table>
        </material-card>
    </div>
</template>

<script>
import MaterialCard from '@/components/material/Card'
import Api from "@/api/resources/article";
import categoryApi from "@/api/resources/category";
import qs from "qs";

export default {
    components: {
        MaterialCard
    },
    props: {
        color: {
            type: String,
            default: 'accent'
        },
        headers: {
            type: Array,
            default: () => {
                return [
                    {
                        sortable: false,
                        key: 'title'
                    },
                    {
                        sortable: false,
                        key: 'view'
                    }
                ]
            }
        },
        title: {
            type: String,
            required: true
        },
        subTitle: {
            type: String,
            required: false
        }
    },
    data() {
        return {
            loading: false,
            articles: [],
            currentPage: 1,
        }
    },
    methods: {
        getData() {
            this.loading = true;
            const query = qs.stringify(this.filter, {encode: false, skipNulls: true});

            Api.list(this.currentPage, query).then(res => {
                this.articles = res.data.popular.original.data;
                this.loading = false;
            }).catch(err => {
                this.loading = false;
            })
        },
        paginate(current_page) {
            this.currentPage = current_page;
            this.getData()
        }
    },

    async created() {
        await this.getData();
    }
}
</script>

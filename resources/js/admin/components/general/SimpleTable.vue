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
                    <tr>
                        <td> {{ articles[0].title }}</td>
                        <td> {{ articles[0].viewed }}</td>
                    </tr>
                    <tr>
                        <td> {{ articles[1].title }}</td>
                        <td> {{ articles[1].viewed }}</td>
                    </tr>
                    <tr>
                        <td> {{ articles[2].title }}</td>
                        <td> {{ articles[2].viewed }}</td>
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
            articles: {},
            editId: null,
            categories: [
                {name: 'All', id: null},
            ],
            statuses: [
                {name: 'All', id: 1},
                {name: 'Published', id: 1},
                {name: 'Pending', id: 0},
            ],
            currentPage: 1,
            filter: {
                search: null,
                category: null,
                is_published: null,
            }
        }
    },
    methods: {
        async getCategories() {
            this.loading = true;
            await categoryApi.getCategories('*').then(res => {
                this.categories = [...this.categories, ...res.data.data];
                this.loading = false;
            });
        },
        getData() {
            this.loading = true;
            const query = qs.stringify(this.filter, {encode: false, skipNulls: true});

            Api.list(this.currentPage, query).then(res => {
                console.log('simpletable',res.data.all.original.data.data);
                this.articles = res.data.all.original.data.data;
                this.currentPage = res.data.all.original.data.current_page;
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
        await this.getCategories();
        await this.getData();
    }
}
</script>

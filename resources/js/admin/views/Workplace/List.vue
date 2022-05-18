<template>
    <v-container fill-height
                 fluid
                 grid-list-xl>
        <v-layout wrap>
            <v-flex>
                <material-card
                    :color="$store.state.app.color"
                    :title="`Workplaces List`"
                    :text="`Use Filter options to filter from workplaces list`"
                >
                    <v-container>
                        <v-row>
                            <v-col cols="12" md="2" class="pr-0">
                                <v-btn :color="$store.state.app.color"
                                       class="float-left"
                                       :to="{ name: 'new-workplace' }">
                                    Create new
                                </v-btn>
                            </v-col>
                            <v-col cols="12" md="4" class="px-0">
                                <VSelectSearchWithValidation v-model="filter.current"
                                                             :options="statuses"
                                                             @change="getData"
                                                             ref="publication_status"
                                                             field="publication_status"
                                                             :label="`Job status`"
                                                             item-text="name"/>
                            </v-col>
                            <v-col cols="12" md="6" class=" pl-0">
                                <VTextFieldWithValidation v-model="filter.search"
                                                          @keyup="getData"
                                                          ref="search"
                                                          field="search"
                                                          :label="'Type...'"/>
                            </v-col>
                        </v-row>

                        <v-simple-table>
                            <template v-slot:default v-if="workplaces">
                                <thead>
                                <tr class="tableHead">
                                    <th class="text-left">Workplace</th>
                                    <th class="text-left">Time Period</th>
                                    <th class="text-left">Designation</th>
                                    <th class="text-left">Languages</th>
                                    <th class="text-left">Current Job</th>
                                    <th class="text-left">Actions</th>
                                </tr>
                                </thead>
                                <tbody v-if="loading" style="height: 100vh;">
                                <v-progress-circular
                                    indeterminate
                                    color="green"/>
                                </tbody>
                                <tbody v-else>
                                <tr v-for="(workplace, index) in workplaces.data" :key="index">
                                    <td style="display: flex; align-items: center;">
                                        <span>
                                            {{ workplace.company_name }}
                                        </span>
                                    </td>
                                    <td> {{ workplace.from }}- {{ workplace.to }}</td>
                                    <td> {{ workplace.designation }}</td>
                                    <td> {{ workplace.language }}</td>
                                    <td>
                                        <v-chip
                                            small
                                            class="ma-2"
                                            text-color="white"
                                            :color="workplace.current? 'success' : 'red'"
                                        >
                                            {{ workplace.current ? 'Yes' : 'No' }}
                                        </v-chip>
                                    </td>
                                    <td>
                                        <v-icon small @click="edit(workplace.id)">mdi-pencil</v-icon>
                                        <v-icon small @click="destroy(workplace.id)">mdi-delete</v-icon>
                                    </td>
                                </tr>
                                </tbody>
                            </template>
                        </v-simple-table>
                        <v-row justify="center">
                            <v-col cols="8">
                                <v-container class="max-width">
                                    <v-pagination
                                        v-model="workplaces.current_page"
                                        :length="workplaces.last_page"
                                        class="my-4"
                                        :total-visible="7"
                                        circle
                                        @input="paginate(workplaces.current_page)"
                                    ></v-pagination>
                                </v-container>
                            </v-col>
                        </v-row>
                    </v-container>
                </material-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
import draggable from 'vuedraggable';
import VTextFieldWithValidation from "@/components/inputs/VTextFieldWithValidation";
import MaterialCard from '@/components/material/Card'
import {ValidationObserver} from 'vee-validate'
import WorkplaceApi from "@/api/resources/workplace";
import VSelectSearchWithValidation from "@/components/inputs/VSelectSearchWithValidation";
import qs from "qs";

export default {
    components: {
        VSelectSearchWithValidation,
        VTextFieldWithValidation,
        ValidationObserver,
        draggable,
        MaterialCard
    },
    data() {
        return {
            loading: false,
            workplaces: {},
            editId: null,
            categories: [
                {name: 'All', id: null},
            ],
            statuses: [
                {name: 'All', id: null},
                {name: 'Current', id: 1},
                {name: 'Previous', id: 0},
            ],
            currentPage: 1,
            filter: {
                search: null,
                current: null
            }
        }
    },
    methods: {
        getData() {
            this.loading = true;
            const query = qs.stringify(this.filter, {encode: false, skipNulls: true});

            WorkplaceApi.list(this.currentPage, query).then(res => {
                this.workplaces = res.data.data;
                this.currentPage = res.data.data.current_page;
                this.loading = false;
            }).catch(err => {
                this.loading = false;
            })
        },
        paginate(current_page) {
            this.currentPage = current_page;
            this.getData()
        },
        edit(slug) {
            this.$router.push({name: 'edit-workplace', params: {slug: slug}});
        },
        destroy(id) {
            if (confirm('Are you sure?')) {
                this.loading = true;
                WorkplaceApi.delete(id).then(res => {
                    this.loading = false;
                    this.getData();
                }).catch(err => {
                    this.loading = false;
                })
            }
        }
    },
    async created() {
        await this.getData();
    }
}
</script>

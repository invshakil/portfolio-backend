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
                                       :to="{ name: 'new-education' }">
                                    Create new
                                </v-btn>
                            </v-col>
                            <v-col cols="12" md="10" class=" pl-0">
                                <VTextFieldWithValidation v-model="filter.search"
                                                          @keyup="getData"
                                                          ref="search"
                                                          field="search"
                                                          :label="'Search by Institution...'"/>
                            </v-col>
                        </v-row>

                        <v-simple-table>
                            <template v-slot:default v-if="educations">
                                <thead>
                                <tr class="tableHead">
                                    <th class="text-left">Institute</th>
                                    <th class="text-left">Session</th>
                                    <th class="text-left">Group</th>
                                    <th class="text-left">Degree Name</th>
                                    <th class="text-left">Result</th>
                                    <th class="text-left">Actions</th>
                                </tr>
                                </thead>
                                <tbody v-if="loading" style="height: 100vh;">
                                <v-progress-circular
                                    indeterminate
                                    color="green"/>
                                </tbody>
                                <tbody v-else>
                                <tr v-for="(education, index) in educations.data" :key="index">
                                    <td style="display: flex; align-items: center;">
                                        <span>
                                            {{ education.institute }}
                                        </span>
                                    </td>
                                    <td> {{ education.session }}</td>
                                    <td> {{ education.subject }}</td>
                                    <td> {{ education.degree }}</td>
                                    <td> {{ education.result }}</td>

                                    <td>
                                        <v-icon small @click="edit(education.id)">mdi-pencil</v-icon>
                                        <v-icon small @click="destroy(education.id)">mdi-delete</v-icon>
                                    </td>
                                </tr>
                                </tbody>
                            </template>
                        </v-simple-table>
                        <v-row justify="center">
                            <v-col cols="8">
                                <v-container class="max-width">
                                    <v-pagination
                                        v-model="educations.current_page"
                                        :length="educations.last_page"
                                        class="my-4"
                                        :total-visible="7"
                                        circle
                                        @input="paginate(educations.current_page)"
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
import EducationApi from "@/api/resources/education";
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
            educations: {},
            editId: null,
            categories: [
                {name: 'All', id: null},
            ],
            currentPage: 1,
            filter: {
                search: null,
            }
        }
    },
    methods: {
        getData() {
            this.loading = true;
            const query = qs.stringify(this.filter, {encode: false, skipNulls: true});

            EducationApi.list(this.currentPage, query).then(res => {
                console.log('ed',res.data.data)
                this.educations = res.data.data;
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
            this.$router.push({name: 'edit-education', params: {slug: slug}});
        },
        destroy(id) {
            if (confirm('Are you sure?')) {
                this.loading = true;
                EducationApi.delete(id).then(res => {
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

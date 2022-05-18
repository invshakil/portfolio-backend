<template>
    <v-container fill-height
                 fluid
                 grid-list-xl>
        <v-layout wrap>
            <v-flex>
                <material-card
                    :color="$store.state.app.color"
                    :title="`Projects List`"
                    :text="`Use Filter options to filter from projects list`"
                >
                    <v-container>
                        <v-row>
                            <v-col cols="12" md="2" class="pr-0">
                                <v-btn :color="$store.state.app.color"
                                       class="float-left"
                                       :to="{ name: 'new-project' }">
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
                            <template v-slot:default v-if="projects">
                                <thead>
                                <tr class="tableHead">
                                    <th class="text-left">Project Cover</th>
                                    <th class="text-left">Project Title</th>
                                    <th class="text-left">Service</th>
                                    <th class="text-left">Demo Link</th>
                                    <th class="text-left">Actions</th>
                                </tr>
                                </thead>
                                <tbody v-if="loading" style="height: 100vh;">
                                    <v-progress-circular
                                        indeterminate
                                        color="green"/>
                                </tbody>
                                <tbody v-else>
                                <tr v-for="(project, index) in projects.data" :key="index">
                                    <td style="padding:5px">
                                        <img v-bind:src="'/' + project.image"
                                             :alt="project.title"
                                             width="60"
                                             style="border-radius: 10px; width:80px; object-fit: contain">
                                    </td>
                                    <td> {{ project.name }}</td>
                                    <td style="display: none"> {{serviceName=services.data.filter((service)=>
                                        (service.id===project.service_id))}}
                                    </td>
                                    <td>{{serviceName[0].name}}</td>
                                    <td> {{ project.demo_link }}</td>
                                    <td>
                                        <v-icon small @click="edit(project.id)">mdi-pencil</v-icon>
                                        <v-icon small @click="destroy(project.id)">mdi-delete</v-icon>
                                    </td>
                                </tr>
                                </tbody>
                            </template>
                        </v-simple-table>
                        <v-row justify="center">
                            <v-col cols="8">
                                <v-container class="max-width">
                                    <v-pagination
                                        v-model="projects.current_page"
                                        :length="projects.last_page"
                                        class="my-4"
                                        :total-visible="7"
                                        circle
                                        @input="paginate(projects.current_page)"
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
import ProjectsApi from "@/api/resources/projects";
import ServicesApi from "@/api/resources/services";
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
            projects: [],
            services: [],
            serviceName:[],
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
        async getData() {
            this.loading = true;
            const query = qs.stringify(this.filter, {encode: false, skipNulls: true});

            await ServicesApi.getServices(this.currentPage).then(res => {
                this.services = res.data.data;
                this.currentPage = res.data.data.current_page;
                this.loading = false;
            }).catch(err => {
                this.loading = false;
            })

            await ProjectsApi.list(this.currentPage, query).then(res => {
                this.projects = res.data.data;
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
            this.$router.push({name: 'edit-projects', params: {slug: slug}});
        },
        destroy(id) {
            if (confirm('Are you sure?')) {
                this.loading = true;
                ProjectsApi.delete(id).then(res => {
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
    },
    watch: {
        services: {
            handler(val) {

            },
            deep: true
        },
    }
}
</script>

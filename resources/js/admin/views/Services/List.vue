<template>
    <v-container fill-height
                 fluid
                 grid-list-xl>
        <v-layout wrap>
            <v-flex>
                <material-card
                    :color="$store.state.app.color"
                    :title="`Services List`"
                    :text="`Edit, create new or delete a service`"
                >
                    <v-container>
                        <v-row>
                            <v-col cols="12" sm="6">
                                <v-btn color="primary" class="float-left" dark @click="openForm">
                                    Create new
                                </v-btn>
                            </v-col>
                        </v-row>

                        <v-row justify="center">
                            <v-dialog
                                v-model="dialog"
                                :persistent="true"
                                max-width="600px"
                                hide-overlay
                                transition="dialog-top-transition"
                            >
                                <ValidationObserver ref="obs" v-slot="{ invalid, validated, handleSubmit, validate }">
                                    <v-form>
                                        <v-card>
                                            <v-card-title>
                                                <span class="headline">New Service</span>
                                            </v-card-title>
                                            <v-card-text>
                                                <v-container>
                                                    <v-row>
                                                        <v-col cols="12" sm="12" md="12">
                                                            <VTextFieldWithValidation v-model="form.name"
                                                                                      rules="required"
                                                                                      :errorValidate="errors.name"
                                                                                      ref="name"
                                                                                      field="name"
                                                                                      :label="'Service Name*'"/>

                                                        </v-col>

                                                        <v-col cols="12" sm="12" md="12">
                                                            <VTextFieldWithValidation v-model="form.description"
                                                                                      rules="required"
                                                                                      ref="excerpt"
                                                                                      field="excerpt"
                                                                                      :label="'Service Description*'"/>

                                                        </v-col>
                                                        <v-col cols="12" sm="12" md="12">
                                                            <VTextFieldWithValidation v-model="form.icon_class"
                                                                                      ref="icon_class"
                                                                                      field="icon_class"
                                                                                      :label="'Icon Class*'"/>

                                                        </v-col>

                                                    </v-row>

                                                </v-container>
                                                <small>*indicates required field</small>
                                            </v-card-text>
                                            <v-card-actions>
                                                <v-spacer></v-spacer>
                                                <v-btn
                                                    color="blue darken-1"
                                                    text
                                                    @click="dialog = false"
                                                >
                                                    Close
                                                </v-btn>
                                                <v-btn
                                                    color="blue darken-1"
                                                    :loading="loading"
                                                    @click="handleSubmit(save)"
                                                >
                                                    Save
                                                </v-btn>
                                            </v-card-actions>
                                        </v-card>
                                    </v-form>
                                </ValidationObserver>
                            </v-dialog>
                        </v-row>


                        <v-simple-table>
                            <template v-slot:default v-if="services">
                                <thead>
                                <tr>
                                    <th class="text-left">
                                        Name
                                    </th>
                                    <th class="text-left">
                                        Description
                                    </th>
                                    <th class="text-left">
                                        Icon Class
                                    </th>
                                    <th class="text-left">
                                        Actions
                                    </th>
                                </tr>
                                </thead>
                                <draggable v-if="loading" style="height: 100vh;">
                                    <v-progress-circular
                                        indeterminate
                                        color="green"/>
                                </draggable>
                                <draggable
                                    v-else
                                    :list="services.data"
                                    tag="tbody"
                                    @change="priorityChange"
                                >
                                    <tr
                                        v-for="(service, index) in services.data"
                                        :key="index"
                                    >
                                        <td> {{ service.name }}</td>
                                        <td> {{ service.description }}</td>
                                        <td> {{ service.icon_class }}</td>
                                        <td>
                                            <v-icon
                                                :loading="loading"
                                                small
                                                @click="edit(service.id)"
                                            >
                                                mdi-pencil
                                            </v-icon>

                                            <v-icon
                                                :loading="loading"
                                                small
                                                @click="destroy(service.id)"
                                            >
                                                mdi-delete
                                            </v-icon>
                                        </td>
                                    </tr>
                                </draggable>
                            </template>
                        </v-simple-table>
                    </v-container>
                </material-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
import draggable from 'vuedraggable';
import VTextFieldWithValidation from "@/components/inputs/VTextFieldWithValidation";
import {ValidationObserver} from 'vee-validate'
import servicesApi from "@/api/resources/services";
import VRadioInputWithValidation from "@/components/inputs/VRadioInputWithValidation";
import MaterialCard from '@/components/material/Card'

export default {
    components: {
        VRadioInputWithValidation,
        VTextFieldWithValidation,
        ValidationObserver,
        draggable,
        MaterialCard
    },
    data() {
        return {
            dialog: false,
            errors:'',
            loading: false,
            services: {},
            editId: null,
            form: {
                name: '',
                description: '',
                icon_class: ''
            }
        }
    },
    methods: {
        priorityChange(data) {
            console.log(data);
        },
        index(page = 1) {
            this.loading = true;
            servicesApi.getServices(page).then(res => {
                this.services = res.data.data;
                this.loading = false;
            }).catch(err => {
                this.loading = false;
            })
        },
        openForm() {
            this.form = {
                name: '',
                description: '',
                icon_class: ''
            };
            this.dialog = true
        },
        edit(id) {
            this.loading = true;
            servicesApi.getDetails(id).then(res => {
                for (const key of Object.keys(this.form)) {
                    this.form[key] = res.data.data[key];
                }
                this.dialog = true;
                this.loading = false;
                this.editId = id;
            }).catch(err => {
                this.loading = false;
            })
        },
        save() {
            this.loading = true;
            let url;

            if (this.editId === null) {
                url = servicesApi.saveService(this.form);
            } else {
                url = servicesApi.updateService(this.form, this.editId);
            }

            url.then(res => {
                this.index();
                this.$toastr.s('Data Saved Successfully')
                this.loading = false;
                this.dialog = false;
            }).catch(err => {
                this.errors=err.data.errors
                this.$toastr.e('Something went wrong!');
                this.loading = false;
            })
        },
        destroy(id) {
            if (confirm('Are you sure?')) {
                this.loading = true;
                servicesApi.deleteService(id).then(res => {
                    this.loading = false;
                    this.$toastr.s('Successfully Deleted')
                    this.index();
                }).catch(err => {
                    this.loading = false;
                })
            }
        }
    },
    created() {
        this.index();
    }
}
</script>

<template>
    <v-container fill-height
                 fluid
                 grid-list-xl>
        <v-layout wrap>
            <v-flex>
                <material-card
                    :color="$store.state.app.color"
                    :title="`Settings List`"
                    :text="`Edit, create new or delete a Settings`"
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
                                                <span class="headline">Settings</span>
                                            </v-card-title>
                                            <v-card-text>
                                                <v-container>
                                                    <v-row>
                                                        <v-col cols="12" sm="12" md="12">
                                                            <VTextFieldWithValidation v-model="form.key"
                                                                                      rules="required"
                                                                                      ref="key"
                                                                                      :disabled="editId"
                                                                                      field="key"
                                                                                      :label="'Info Title*'"/>

                                                        </v-col>

                                                        <v-col cols="12" sm="12" md="12">
                                                            <VTextFieldWithValidation v-model="form.value"
                                                                                      rules="required"
                                                                                      ref="value"
                                                                                      field="value"
                                                                                      :label="'Information*'"/>

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
                                                    @click="()=> {
                                                           dialog = false
                                                           editId = null;
                                                    }"
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
                            <template v-slot:default v-if="aboutMes">
                                <thead>
                                <tr>
                                    <th class="text-left">
                                        Key
                                    </th>
                                    <th class="text-left">
                                        Detail
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
                                    :list="aboutMes.data"
                                    tag="tbody"
                                    @change="priorityChange"
                                >
                                    <tr
                                        v-for="(aboutMe, index) in aboutMes.data"
                                        :key="index"
                                    >
                                        <td> {{ aboutMe.key }}</td>
                                        <td> {{ aboutMe.value }}</td>
                                        <td>
                                            <v-icon
                                                :loading="loading"
                                                small
                                                @click="edit(aboutMe.id)"
                                            >
                                                mdi-pencil
                                            </v-icon>

                                            <v-icon
                                                :loading="loading"
                                                small
                                                @click="destroy(aboutMe.id)"
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
import settingsApi from "@/api/resources/settings";
import MaterialCard from '@/components/material/Card'
import VRadioInputWithValidation from "@/components/inputs/VRadioInputWithValidation";

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
            loading: false,
            aboutMes: {},
            editId: null,
            form: {
                key: '',
                value: '',
            }
        }
    },
    methods: {
        priorityChange(data) {
            console.log(data);
        },
        index(page = 1) {
            this.loading = true;
            settingsApi.get(page).then(res => {
                console.log('da',res.data)
                this.aboutMes = res.data.data;
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
            settingsApi.getDetails(id).then(res => {
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
                url = settingsApi.save(this.form);
            } else {
                url = settingsApi.update(this.form, this.editId);
            }

            url.then(res => {
                this.loading = false;
                this.dialog = false;
                this.$toastr.s('Data Saved Successfully')
                this.index();
            }).catch(err => {
                this.loading = false;
            })
            this.editId = null;
        },
        destroy(id) {
            if (confirm('Are you sure?')) {
                this.loading = true;
                settingsApi.delete(id).then(res => {
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

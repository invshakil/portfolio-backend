<template>
    <v-container fill-height
                 fluid
                 grid-list-xl>
        <v-layout wrap>
            <v-flex>
                <material-card
                    :color="$store.state.app.color"
                    :title="`Skills Page`"
                    :text="`Edit, create new or delete a Skills`"
                >
                    <v-container>

                        <v-row justify="center">
                            <v-dialog
                                v-model="dialog"
                                :persistent="true"
                                width="100%"
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
                                                        <v-col cols="12" md="12">
                                                            <vue-editor id="editor"
                                                                        :editorOptions="editorConfig"
                                                                        v-model="form.description"/>
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
                            <template v-slot:default v-if="skills">
                                <thead>
                                <tr>
                                    <th class="text-left">
                                        Skills Page
                                    </th>
                                    <th class="text-left">
                                        Edit Page
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
                                    :list="skills.data"
                                    tag="tbody"
                                    @change="priorityChange"
                                >
                                    <tr
                                        v-for="(skill, index) in skills.data"
                                        :key="index"
                                    >
                                        <td v-html= skill.description />
                                        <td>
                                            <v-icon
                                                :loading="loading"
                                                x-large
                                                color="green"
                                                @click="edit(skill.id)"
                                            >
                                                mdi-pencil
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
import skillsAPi from "@/api/resources/skills";
import VRadioInputWithValidation from "@/components/inputs/VRadioInputWithValidation";
import MaterialCard from '@/components/material/Card'
import {Quill, VueEditor} from "vue2-editor";
import {ImageDrop} from 'quill-image-drop-module'
import ImageResize from 'quill-image-resize-module'

Quill.register('modules/imageResize', ImageResize)
Quill.register('modules/imageDrop', ImageDrop)

export default {
    components: {
        VRadioInputWithValidation,
        VTextFieldWithValidation,
        ValidationObserver,
        draggable,
        MaterialCard,
        VueEditor
    },
    data() {
        return {
            editorConfig: {
                modules: {
                    imageDrop: true,
                    imageResize: {}
                }
            },
            dialog: false,
            loading: false,
            skills: {},
            editId: null,
            form: {
                description: '',
            }
        }
    },
    methods: {
        priorityChange(data) {
            console.log(data);
        },
        index(page = 1) {
            this.loading = true;
            skillsAPi.get(page).then(res => {
                this.skills = res.data.data;
                this.loading = false;
            }).catch(err => {
                this.loading = false;
            })
        },
        openForm() {
            this.form = {
                description: '',
            };
            this.dialog = true
        },
        edit(id) {
            this.loading = true;
            skillsAPi.getDetails(id).then(res => {
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
                url = skillsAPi.save(this.form);
            } else {
                url = skillsAPi.update(this.form, this.editId);
            }

            url.then(res => {
                this.index();
                this.$toastr.s('Data Saved Successfully')
                this.loading = false;
                this.dialog = false;
            }).catch(err => {
                this.loading = false;
            })
        },
        destroy(id) {
            if (confirm('Are you sure?')) {
                this.loading = true;
                skillsAPi.delete(id).then(res => {
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

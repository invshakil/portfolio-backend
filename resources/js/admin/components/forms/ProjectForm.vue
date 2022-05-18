<template>

    <material-card
        :color="$store.state.app.color"
        :title="title"
        :text="shortDescription"
    >
        <ValidationObserver ref="obs" v-slot="{ invalid, validated, handleSubmit, validate }">

            <v-form>
                <v-container>
                    <v-row>
                        <v-col cols="12" md="6">
                            <VTextFieldWithValidation v-model="form.name" rules="required" ref="name"
                                                      field="name"
                                                      :label="'Title Of the project*'"/>
                        </v-col>


                        <v-col cols="12" md="6">
                            <VTextFieldWithValidation v-model="form.tags"
                                                      rules="required"
                                                      ref="tags"
                                                      field="tags"
                                                      :label="'Tags*'"
                                                      @click.stop="dialog = true"/>
                        </v-col>

                        <v-col cols="12" sm="12" md="6">
                            <VTextFieldWithValidation v-model="form.demo_link"
                                                      rules="required"
                                                      ref="demo_link"
                                                      field="demo_link"
                                                      :label="'Link of the demo project*'"
                                                      @click.stop="dialog2 = true"/>
                        </v-col>

                        <v-col cols="12" md="6">
                            <VSelectSearchWithValidation v-if="services.data"
                                                         rules="required"
                                                         v-model="form.service_id"
                                                         :options="services.data"
                                                         ref="service_id"
                                                         field="service_id"
                                                         :label="`Service`"
                                                         item-text="name"
                                                         item-value="id"/>
                        </v-col>

                        <v-col cols="12" md="12">
                            <vue-editor id="editor"
                                        :editorOptions="editorConfig"
                                        v-model="form.description"/>
                        </v-col>

                        <v-col cols="12" md="12">
                            <VFileInputWithValidation v-model="form.image"
                                                      :image-url="form.image_url"
                                                      :rules="!editKey ? `required` : ''"
                                                      ref="image"
                                                      field="image"
                                                      isRow
                                                      :label="'Project Cover Image*'"/>
                        </v-col>

                    </v-row>
                    <v-row>
                        <v-col style="text-align: center">
                            <v-btn
                                :loading="loading"
                                depressed color="primary" @click="handleSubmit(save)">
                                Save
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-container>
            </v-form>
        </ValidationObserver>
    </material-card>


</template>
<script>
import MaterialCard from '@/components/material/Card'
import VTextAreaFieldWithValidation from "@/components/inputs/VTextAreaFieldWithValidation";
import VTextFieldWithValidation from "@/components/inputs/VTextFieldWithValidation";
import VSelectSearchWithValidation from "@/components/inputs/VSelectSearchWithValidation";
import VFileInputWithValidation from "@/components/inputs/VFileInputWithValidation";
import {ValidationObserver} from 'vee-validate'
import ServicesApi from "@/api/resources/services";
import ProjectsApi from "@/api/resources/projects";
import VRadioInputWithValidation from "@/components/inputs/VRadioInputWithValidation";
import {Quill, VueEditor} from "vue2-editor";
import {ImageDrop} from 'quill-image-drop-module'
import ImageResize from 'quill-image-resize-module'

Quill.register('modules/imageResize', ImageResize)
Quill.register('modules/imageDrop', ImageDrop)

export default {
    components: {
        VRadioInputWithValidation,
        VTextAreaFieldWithValidation,
        VTextFieldWithValidation,
        VSelectSearchWithValidation,
        VFileInputWithValidation,
        ValidationObserver,
        MaterialCard,
        VueEditor

    },
    props: {
        title: {
            type: String,
            default: "Project Form"
        },
        shortDescription: {
            type: String,
            default: ""
        },
        editKey: {
            type: String,
            required: false
        }
    },
    data() {
        return {
            editorConfig: {
                modules: {
                    imageDrop: true,
                    imageResize: {}
                }
            },
            loading: false,
            dialog: false,
            dialog2: false,
            services: [],
            form: {
                name: '',
                image: '',
                service_id: '',
                description: '',
                tags: '',
                demo_link: '',
            }
        }
    },
    async mounted() {
        if (this.editKey) {
            await this.get();
        }
    },
    async created() {
        await this.getServices();
    },
    methods: {
        async get() {
            this.loading = true;
            ProjectsApi.get(this.editKey).then(res => {
                this.form = res.data.data;
                this.loading = false;
            }).catch(err => {
                this.$toastr.e('Something went wrong! ' + err);
                this.loading = false;
            })
        },
        getServices(){
            ServicesApi.getServices(this.currentPage).then(res => {
                this.services = res.data.data;
                this.loading = false;
            }).catch(err => {
                this.loading = false;
            })
        },
        save() {
            this.loading = true;
            let reqUrl;

            if (this.editKey) {
                reqUrl = ProjectsApi.update(this.form)
            } else {
                reqUrl = ProjectsApi.save(this.form)
            }

            reqUrl.then(res => {
                this.$toastr.s('Data saved successful');
                this.$router.push({name: 'projects'});
                this.loading = false;
            }).catch(err => {
                this.$toastr.e('Something went wrong! ' + err);
                this.loading = false;
            })
        }
    }
}
</script>
<style>
#editor {
    min-height: 350px !important;
}

#editor p {
    margin: 0 0 10px 0;
}
</style>

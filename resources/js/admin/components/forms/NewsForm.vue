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
                            <VTextFieldWithValidation v-model="form.title" rules="required" ref="title"
                                                      field="title"
                                                      :label="'title*'"/>
                        </v-col>

                        <v-col cols="12" md="4">
                            <VRadioInputWithValidation field="published"
                                                       :rules="'required'"
                                                       :options="[{label: $t('Common.yes'), value: 1}, {label: $t('Common.no'), value: 0}]"
                                                       v-model="form.published"/>
                        </v-col>

                        <v-col cols="12" md="12">
                            <span>Description (in exact way you want on website)</span>
                            <vue-editor :editorOptions="editorConfig"
                                        v-model="form.description"/>
                        </v-col>

                        <v-col cols="12" md="12">
                            <VFileInputWithValidation v-model="form.image"
                                                      :rules="!newsKey ? `required` : ''"
                                                      ref="image"
                                                      field="image"
                                                      :isRow="true"
                                                      :label="'Featured Cover Image*'"/>
                        </v-col>

                    </v-row>
                    <v-row>
                        <v-col style="text-align: center">
                            <v-btn :loading="loading" depressed color="primary" @click="handleSubmit(save)">
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
import newsApi from "@/api/resources/news";
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
            default: "News Form"
        },
        shortDescription: {
            type: String,
            default: ""
        },
        newsKey: {
            type: String,
            required: false
        }
    },
    data() {
        return {
            locale: this.$i18n.locale,
            loading: false,
            editorConfig: {
                modules: {
                    imageDrop: true,
                    imageResize: {}
                }
            },
            form: {
                title: '',
                published: 0,
                description: '',
                image: '',
            }
        }
    },
    async mounted() {
        if (this.newsKey) {
            await this.get();
        }
    },
    methods: {
        async get() {
            this.loading = true;
            newsApi.get(this.newsKey).then(res => {
                this.form = res.data.data;
                this.loading = false;
            }).catch(err => {
                this.$toastr.e('Something went wrong! ' + err);
                this.loading = false;
            })
        },
        save() {
            this.loading = true;
            let reqUrl;

            if (this.newsKey) {
                reqUrl = newsApi.update(this.form)
            } else {
                reqUrl = newsApi.save(this.form)
            }

            reqUrl.then(res => {
                this.$toastr.s('Data saved successful');
                this.$router.push({name: 'news'});
                this.loading = false;
            }).catch(err => {
                this.$toastr.e('Something went wrong! ' + err);
                this.loading = false;
            })
        }
    },
}
</script>
<style>
.editor {
    min-height: 300px !important;
}

#editor p {
    margin: 0 0 10px 0;
}
</style>

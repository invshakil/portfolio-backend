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
                                                      :label="'Title*'"/>
                        </v-col>

                        <v-col cols="12" md="6">
                            <VTextFieldWithValidation v-model="form.meta_title"
                                                      rules="required"
                                                      ref="meta_title"
                                                      field="meta_title" :label="'Meta title'"/>
                        </v-col>

                        <v-col cols="12" md="12" @click="dialog = true" style="cursor: pointer">
                            <v-container fluid>
                                <VTextFieldWithValidation v-model="form.categories"
                                                          rules="required"
                                                          disabled
                                                          ref="category"
                                                          field="category"
                                                          :label="'Categories*'"
                                                          placeholder="Category"
                                                          hint="comma (,) separated"/>
                                <v-dialog
                                    v-model="dialog"
                                    hide-overlay
                                    max-width="600"
                                >
                                    <v-card>
                                        <v-card-title class="text-h5">
                                            Choose categories for the article
                                        </v-card-title>

                                        <v-card-text>
                                            <div v-for="(category, index) in categories" :key="index">
                                                <v-checkbox
                                                    v-model="form.categories"
                                                    :label="category.name"
                                                    :value="category.name"
                                                ></v-checkbox>
                                            </div>
                                        </v-card-text>

                                        <v-card-actions>
                                            <v-spacer></v-spacer>

                                            <v-btn
                                                color="success"
                                                @click="dialog = false"
                                            >
                                                Save
                                            </v-btn>

                                            <v-btn
                                                color="default"
                                                @click="()=>{
                                                  dialog = false
                                                  form.categories=[]}">
                                                Reset
                                            </v-btn>
                                        </v-card-actions>
                                    </v-card>
                                </v-dialog>
                            </v-container>
                        </v-col>

                        <v-col cols="12" md="12">
                            <vue-editor id="editor"
                                        :editorOptions="editorConfig"
                                        v-model="form.description"/>
                        </v-col>

                        <v-col cols="12" md="6">
                            <VTextAreaFieldWithValidation v-model="form.meta_description"
                                                          rules="required"
                                                          ref="meta_description"
                                                          rows="2"
                                                          field="short_description"
                                                          :label="'Short Description*'"
                                                          hint="For SEO Meta description it will be displayed"/>
                        </v-col>

                        <v-col cols="12" sm="12" md="6">
                            <VTextAreaFieldWithValidation v-model="form.tags"
                                                          rules="required"
                                                          rows="2"
                                                          ref="tags"
                                                          field="keywords"
                                                          :label="'Article Tags*'"
                                                          placeholder="seo keyword"
                                                          hint="comma (,) separated"/>

                        </v-col>
                        <v-col cols="12" md="6">
                            <VRadioInputWithValidation field="published"
                                                       :rules="'required'"
                                                       :options="[{label: 'Yes', value: 1}, {label: 'No', value: 0}]"
                                                       v-model="form.status"/>
                        </v-col>

                        <v-col cols="12" md="6">
                            <VRadioInputWithValidation field="featured"
                                                       :rules="'required'"
                                                       :options="[{label: 'Yes', value: 1}, {label: 'No', value: 0}]"
                                                       v-model="form.slider_status"/>
                        </v-col>

                        <v-col cols="12" md="12">
                            <VFileInputWithValidation v-model="form.image"
                                                      :image-url="form.image_url"
                                                      :rules="!articleKey ? `required` : ''"
                                                      ref="image"
                                                      field="image"
                                                      :isRow="true"
                                                      :label="'Article Cover Image*'"/>
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
import articleApi from "@/api/resources/article";
import VRadioInputWithValidation from "@/components/inputs/VRadioInputWithValidation";
import categoryApi from "@/api/resources/category";
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
            default: "Article Form"
        },
        shortDescription: {
            type: String,
            default: ""
        },
        articleKey: {
            type: String,
            required: false
        }
    },
    data() {
        return {
            loading: false,
            editorConfig: {
                modules: {
                    imageDrop: true,
                    imageResize: {}
                }
            },
            categories: [],
            categoryInputs: [],
            dialog: false,
            form: {
                title: '',
                categories: [],
                meta_description: '',
                meta_title: '',
                status: 1,
                slider_status: 0,
                description: '',
                tags: '',
                image: '',
            }
        }
    },
    async mounted() {
        await this.getCategories();

        if (this.articleKey) {
            await this.get();
        }
    },
    methods: {
        async getCategories() {
            this.loading = true;
            await categoryApi.getCategories('*').then(res => {
                this.categories = res.data.data;
                this.loading = false;
            });
        },
        async get() {
            this.loading = true;
            articleApi.get(this.articleKey).then(res => {
                res.data.data.image = null;

                if (res.data.data.categories.length) {
                    res.data.data.categories = res.data.data.categories.map(item => item.name).toString() // for now multi category selection is not possible
                }

                if (res.data.data.tags.length) {
                    res.data.data.tags = res.data.data.tags.map(item => item.name).toString();
                }

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

            if (this.articleKey) {
                reqUrl = articleApi.update(this.form)
            } else {
                reqUrl = articleApi.save(this.form)
            }

            reqUrl.then(res => {
                this.$toastr.s('Data saved successful');
                this.$router.push({name: 'articles'});
                this.loading = false;
            }).catch(err => {
                this.$toastr.e('Something went wrong! ' + err);
                this.loading = false;
            })
        }
    },
    watch: {
        categoryInputs: {
            handler(val) {
                // console.log('val',val)
                this.form.categories.push(val)
                console.log(this.form.categories)
            },
            deep: true
        },

    }
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

<template>
    <v-container>
        <v-layout justify-center wrap>
            <v-flex md12>
                <material-card
                    :color="$store.state.app.color"
                    :title="`System Settings`"
                >

                    <v-container>
                        <v-row>
                            <v-col cols="12" md="6">
                                <VTextFieldWithValidation v-model="form.app_name"
                                                          ref="app_name"
                                                          field="app_name"
                                                          :label="'App Name'"/>
                            </v-col>
                            <v-col cols="12" md="6">
                                <VTextFieldWithValidation v-model="form.app_version"
                                                          ref="app_version"
                                                          field="app_version"
                                                          :label="'App Version'"/>
                            </v-col>

                            <v-col cols="12" md="4">
                                <VTextFieldWithValidation v-model="form.home_page_title"
                                                          ref="home_page_title"
                                                          field="home_page_title"
                                                          :label="'Home page title'"/>
                            </v-col>

                            <v-col cols="6" md="6">
                                <v-file-input v-model="form.home_page_image"
                                              ref="image"
                                              field="image"
                                              outlined
                                              show-size
                                              small-chips
                                              accept="image/*"
                                              :label="'App Logo'"
                                              prepend-icon="mdi-camera"
                                              @change="onFileChange"
                                />
                                <v-img :src="imageUrl" style="object-fit: cover" v-bind:style= " [form.home_page_image ? '' : {'display': 'none'}]" width="20%" />
                            </v-col>

                        </v-row>
                    </v-container>
                    <v-card-actions style="text-align: center">
                        <v-btn depressed color="primary" @click="updateSettings">
                            Save
                        </v-btn>
                    </v-card-actions>
                </material-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
import VFileInputWithValidation from "@/components/inputs/VFileInputWithValidation";
import Api from "@/api/resources/settings";
import MaterialCard from '@/components/material/Card'
import VTextFieldWithValidation from "@/components/inputs/VTextFieldWithValidation";
import VTextAreaFieldWithValidation from "@/components/inputs/VTextAreaFieldWithValidation";

export default {
    components: {
        VTextAreaFieldWithValidation,
        VTextFieldWithValidation,
        VFileInputWithValidation,
        MaterialCard
    },
    data: () => ({
        imageUrl: "",
        loading: false,
        form: {
            app_name: '',
            app_version: '',
            home_page_title: '',
            home_page_image: '',
            home_page_image_url:'',
        }
    }),
    methods: {
        getSettings() {
            this.loading = true;
            Api.getSettings().then(res => {
                this.form = res.data;
                this.imageUrl=res.data.home_page_image_url
                this.loading = false;
            }).catch(err => {
                this.$toastr.e('Something went wrong! ');
                this.loading = false;
            })
        },
        updateSettings() {
            this.loading = true;
            Api.updateSettings(this.form).then(res => {
                this.$toastr.s('Settings update successful');
                if(this.imageUrl){
                    location.reload();
                }
                this.loading = false;
            }).catch(err => {
                this.$toastr.e('Something went wrong!');
                this.loading = false;
            })
        },
        createImage(file) {
            const reader = new FileReader();
            reader.onload = e => {
                this.imageUrl = e.target.result;
            };
            reader.readAsDataURL(file);
        },
        onFileChange(file) {
            if (!file) {
                return;
            }
            this.createImage(file);
        }
    },
    created() {
        this.getSettings();
    }
}
</script>

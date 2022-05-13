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
                            <VTextFieldWithValidation v-model="form.institute" rules="required" ref="institute"
                                                      field="institute"
                                                      :label="'Institute Name*'"/>
                        </v-col>

                        <v-col cols="12" md="6">
                            <VTextFieldWithValidation v-model="form.session"
                                                      rules="required"
                                                      ref="session"
                                                      field="session" :label="'Session*'"/>
                        </v-col>

                        <v-col cols="12" md="6">
                            <VTextFieldWithValidation v-model="form.subject"
                                                      rules="required"
                                                      ref="subject"
                                                      field="subject" :label="'Subject'"/>
                        </v-col>


                        <v-col cols="12" md="6">
                            <VTextFieldWithValidation v-model="form.degree"
                                                      rules="required"
                                                      ref="degree"
                                                      field="degree"
                                                      :label="'Degree*'"/>
                        </v-col>


                        <v-col cols="12" md="6">
                            <VTextFieldWithValidation v-model="form.result"
                                                          rules="required"
                                                          ref="result"
                                                          field="result"
                                                          :label="'Result (in a format you want it to display)*'"/>
                        </v-col>

                        <v-col cols="12" md="6">
                            <VTextFieldWithValidation v-model="form.label"
                                                          rules="required"
                                                          hidden
                                                          ref="label"
                                                          field="label"
                                                          :label="'label*'"/>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col style="text-align: center">
                            <v-btn :loading="loading"
                                   depressed color="primary"
                                   @click="handleSubmit(save)"
                            >
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
import educationApi from "@/api/resources/education";
import VRadioInputWithValidation from "@/components/inputs/VRadioInputWithValidation";

export default {
    components: {
        VRadioInputWithValidation,
        VTextAreaFieldWithValidation,
        VTextFieldWithValidation,
        VSelectSearchWithValidation,
        VFileInputWithValidation,
        ValidationObserver,
        MaterialCard,
    },
    props: {
        title: {
            type: String,
            default: "Workplace Form"
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
            loading: false,
            dialog: false,
            dialog2: false,
            form: {
                subject: '',
                institute: '',
                degree: '',
                result: '',
                session: '',
                label: 'A',
            }
        }
    },
    async mounted() {
        if (this.editKey) {
            await this.get();
        }
    },
    methods: {
        async get() {
            this.loading = true;
            educationApi.get(this.editKey).then(res => {
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

            if (this.editKey) {
                reqUrl = educationApi.update(this.form)
            } else {
                reqUrl = educationApi.save(this.form)
            }

            reqUrl.then(res => {
                this.$toastr.s('Data saved successful');
                this.$router.push({name: 'education'});
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

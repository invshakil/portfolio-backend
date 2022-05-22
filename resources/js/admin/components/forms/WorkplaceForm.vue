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
                            <VTextFieldWithValidation v-model="form.company_name" rules="required" ref="title"
                                                      field="company_name"
                                                      :errorValidate="errors.company_name"
                                                      :label="'Company Name*'"/>
                        </v-col>

                        <v-col cols="12" md="6">
                            <VTextFieldWithValidation v-model="form.designation"
                                                      rules="required"
                                                      ref="designation"
                                                      field="designation" :label="'Designation'"/>
                        </v-col>

                        <v-col cols="12" md="6">
                            <VTextFieldWithValidation v-model="form.language"
                                                      rules="required"
                                                      ref="language"
                                                      field="language" :label="'Languages Used'"/>
                        </v-col>


                        <v-col cols="12" md="4">
                            <VRadioInputWithValidation field="current"
                                                       :rules="'required'"
                                                       :options="[{label: 'Yes', value: 1}, {label: 'No', value: 0}]"
                                                       v-model="form.current"/>
                        </v-col>

                        <v-col cols="12" md="6">
                            <VTextFieldWithValidation v-model="form.from"
                                                      rules="required"
                                                      ref="from"
                                                      field="from"
                                                      :label="'Job Started On*'"
                                                      placeholder="Job Started On*"
                                                      @click.stop="dialog = true"/>
                        </v-col>

                        <v-col cols="12" sm="12" md="6">
                            <VTextFieldWithValidation v-model="form.to"
                                                      rules="required"
                                                      ref="to"
                                                      field="to"
                                                      :label="'Job Ended On*'"
                                                      placeholder="Job Ended On*"
                                                      @click.stop="dialog2 = true"/>
                        </v-col>

                        <v-col cols="12" md="12">
                            <VTextAreaFieldWithValidation v-model="form.description"
                                                          rules="required"
                                                          ref="description"
                                                          rows="2"
                                                          field="description"
                                                          :label="'Description*'"/>
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


            <v-dialog
                v-model="dialog"
                max-width="600px"
                hide-overlay
            >
                <v-card>
                    <v-date-picker
                        v-model="form.from"
                        color="red lighten-1"
                        rules="required"
                        ref="from"
                        full-width
                        hint="Choose a date"
                        field="from"
                        :label="'Pick A Date *'"
                    />
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="green darken-1"
                            text
                            @click="dialog = false"
                        >
                            Save
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog
                v-model="dialog2"
                max-width="600px"
                hide-overlay
            >
                <v-card>
                    <v-date-picker
                        v-model="form.to"
                        color="red lighten-1"
                        rules="required"
                        ref="to"
                        full-width
                        hint="Choose a date"
                        field="to"
                        :label="'Pick A Date *'"
                    />
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="green darken-1"
                            text
                            @click="dialog2 = false"
                        >
                            Save
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
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
import workplaceApi from "@/api/resources/workplace";
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
            errors: '',
            loading: false,
            dialog: false,
            dialog2: false,
            form: {
                designation: '',
                language: '',
                company_name: '',
                description: '',
                current: 1,
                from: '',
                to: '',
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
            workplaceApi.get(this.editKey).then(res => {
                this.form = res.data.data;
                this.loading = false;
            }).catch(err => {
                this.$toastr.e('Something went wrong! ');
                this.loading = false;
            })
        },
        save() {
            this.loading = true;
            let reqUrl;

            if (this.editKey) {
                reqUrl = workplaceApi.update(this.form)
            } else {
                reqUrl = workplaceApi.save(this.form)
            }

            reqUrl.then(res => {
                this.$toastr.s('Data saved successful');
                this.$router.push({name: 'workplace'});
                this.loading = false;
            }).catch(err => {
                this.errors=err.data.errors
                this.$toastr.e('Something went wrong! ');
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

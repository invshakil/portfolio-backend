<template>
    <material-card
        :color="$store.state.app.color"
        :title="$t('Common.edit_profile')"
        :text="$t('Common.complete_your_profile')"
    >
        <ValidationObserver ref="profileForm" v-slot="{ handleSubmit }">
            <v-form @submit.prevent="handleSubmit(onSubmit)">
                <v-container py-0>
                    <v-layout wrap>

                        <v-flex xs12 md4>
                            <VTextFieldWithValidation :label="$t('Fields.first_name')"
                                                      field="first_name"
                                                      rules="required|min:2"
                                                      v-model="profile.name"
                            />
                        </v-flex>
                        <v-flex xs12 md6>
                            <VTextFieldWithValidation :label="$t('Fields.email')"
                                                      disabled
                                                      field="email"
                                                      rules="required|email"
                                                      v-model="profile.email"
                            />
                        </v-flex>

                        <v-flex
                            xs12
                            text-xs-right
                        >
                            <v-btn :class="`mx-0 font-weight-bold bg-color-${$store.state.app.color}`"
                                   type="submit"
                                   v-text="$t('Common.save_profile')"/>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-form>
        </ValidationObserver>
    </material-card>
</template>

<script>
import {ValidationObserver} from 'vee-validate'
import MaterialCard from '@/components/material/Card'
import VTextFieldWithValidation from '@/components/inputs/VTextFieldWithValidation'
import DatePickerWithValidation from '@/components/inputs/DatePickerWithValidation'
import VRadioInputWithValidation from '@/components/inputs/VRadioInputWithValidation'
import VFileInputWithValidation from '@/components/inputs/VFileInputWithValidation'
import profile from "../../../api/resources/profile";
export default {
    name: 'information-update-card',
    components: {
        ValidationObserver,
        MaterialCard,
        VTextFieldWithValidation,
        DatePickerWithValidation,
        VRadioInputWithValidation,
        VFileInputWithValidation
    },
    data: () => {
        return {
            profile: {
                name: '',
                email: '',
            },
        }
    },
    mounted() {
        const {user} = this.$store.state

        this.profile = {
            name: user.name,
            email: user.email,
        }
    },
    methods: {
        async onSubmit() {
            const validated = await this.$refs.profileForm.validate()
            if (!validated) return
            this.$store.dispatch('saveProfile', this.profile)
                .then(() => this.$store.dispatch('app/setSnackbarMessage', this.$t('Messages.saved_successfully')))
                .catch(() => this.$store.dispatch('app/setSnackbarMessage', this.$t('Messages.something_went_wrong')))
        },
    }
}
</script>

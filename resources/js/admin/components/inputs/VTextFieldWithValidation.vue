<template>
  <ValidationProvider :name="field" :rules="rules" v-slot="{ errors, valid}">
    <v-text-field
        outlined
        v-model="inner_value"
        :error-messages="errorValidate"
        :success="valid"
        v-bind="$attrs"
        v-on="$listeners"
    ></v-text-field>
  </ValidationProvider>
</template>

<script>
import { ValidationProvider } from 'vee-validate'

export default {
  components: {
    ValidationProvider
  },
  props: {
    errorValidate: {
      default: ''
    },
      rules: {
      type: [Object, String],
      default: ''
    },
    value: {
      type: null,
      required: true
    },
    field: {
      type: String,
      required: false,
      default: null
    },
  },
  data: () => ({
    inner_value: ''
  }),
  watch: {
    // Handles internal model changes.
    inner_value (newVal) {
      this.$emit('input', newVal)
    },
    // Handles external model changes.
    value (newVal) {
      this.inner_value = newVal
    }
  },
  async created () {
    if (this.value) {
      this.inner_value = this.value
    }
  }
}
</script>

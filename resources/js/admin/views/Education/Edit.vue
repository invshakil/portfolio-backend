<template>
    <v-container fill-height fluid grid-list-xl>
        <v-layout justify-center wrap>
            <v-flex md12>
                <EducationForm title="Edit Education"
                               short-description="Selecting published will make it displayed in the website"
                               :edit-key="$route.params.slug.toString()"/>
            </v-flex>
        </v-layout>
    </v-container>

</template>
<script>
import EducationForm from "../../components/forms/EducationForm";

export default {
    components: {
        EducationForm,
    }
}
</script>
<style>
.ck-editor__editable {
    min-height: 300px !important;
}
</style>

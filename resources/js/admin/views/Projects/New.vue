<template>
    <v-container fill-height fluid grid-list-xl>
        <v-layout justify-center wrap>
            <v-flex md12>
                <EducationForm title="Create New Education"
                          short-description="Selecting published will make it displayed in the website"
                />
            </v-flex>
        </v-layout>
    </v-container>

</template>
<script>
import EducationForm from "../../components/forms/EducationForm";

export default {
    components: {
        EducationForm,
    },
}
</script>

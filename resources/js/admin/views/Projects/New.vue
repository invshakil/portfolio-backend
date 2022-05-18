<template>
    <v-container fill-height fluid grid-list-xl>
        <v-layout justify-center wrap>
            <v-flex md12>
                <ProjectForm title="Create New Project"
                          short-description="This will be displayed on the websites portfolio page"
                />
            </v-flex>
        </v-layout>
    </v-container>

</template>
<script>
import ProjectForm from "../../components/forms/ProjectForm";

export default {
    components: {
        ProjectForm,
    },
}
</script>

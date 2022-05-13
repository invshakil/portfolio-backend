<template>
    <v-container fill-height fluid grid-list-xl>
        <v-layout justify-center wrap>
            <v-flex md12>
                <ProjectsForm title="Edit Projects"
                               short-description="Selecting published will make it displayed in the website"
                               :edit-key="$route.params.slug"/>
            </v-flex>
        </v-layout>
    </v-container>

</template>
<script>
import ProjectsForm from "@/components/forms/ProjectForm";

export default {
    components: {
        ProjectsForm,
    }
}
</script>
<style>
.ck-editor__editable {
    min-height: 300px !important;
}
</style>

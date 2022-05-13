<template>
    <v-container fill-height fluid grid-list-xl>
        <v-layout justify-center wrap>
            <v-flex md12>
                <WorkplaceForm title="Create New Workplace"
                          short-description="Selecting published will make it displayed in the website"
                />
            </v-flex>
        </v-layout>
    </v-container>

</template>
<script>
import WorkplaceForm from "@/components/forms/WorkplaceForm";

export default {
    components: {
        WorkplaceForm,
    },
}
</script>

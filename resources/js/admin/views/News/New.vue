<template>
    <v-container fill-height fluid grid-list-xl>
        <v-layout justify-center wrap>
            <v-flex md12>
                <NewsForm :title="'Create New Featured'"
                             :short-description="'Description for FeatureSection'"
                />
            </v-flex>
        </v-layout>
    </v-container>

</template>
<script>
import NewsForm from "@/components/forms/NewsForm";

export default {
    components: {
        NewsForm
    },
}
</script>

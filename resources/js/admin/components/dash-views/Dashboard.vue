<template>
    <v-container
        fill-height
        fluid
        grid-list-xl
    >
        <v-layout wrap>
            <v-flex
                sm6
                xs12
                md6
                lg6
            >
                <material-stats-card
                    :color="$store.state.app.color"
                    icon="mdi-book-multiple"
                    :title="`Total Articles`"
                    :value=allArticleCount
                    sub-icon="mdi-calendar"
                    :sub-text= "articleCountInLastDay+' Articles Added In Last 24 Hours'"
                />
            </v-flex>
            <v-flex
                sm6
                xs12
                md6
                lg6
            >
                <material-stats-card
                    :color="$store.state.app.color"
                    icon="mdi-account-group"
                    :title="`  Total Visits`"
                    :value=totalVisitsAllTime
                    sub-icon="mdi-account-check"
                    sub-icon-color="success"
                    :sub-text= "totalVisitsInLastDay+'  Visits in last 24 hours'"
                />
            </v-flex>
            <v-flex
                sm6
                xs12
                md6
                lg6
            >
                <material-stats-card
                    :color="$store.state.app.color"
                    icon="mdi-tag-multiple"
                    :title="`Total Categories`"
                    :value=categories
                    sub-icon="mdi-video"
                    :sub-text="`0 Video published`"
                />
            </v-flex>
            <v-flex
                sm6
                xs12
                md6
                lg6
            >
                <material-stats-card
                    :color="$store.state.app.color"
                    icon="mdi-account-multiple-check "
                    :title="`Unique User Visited`"
                    :value=visitors
                    sub-icon="mdi-update"
                    :sub-text= "visitorsInLastWeek+' Unique users in last week'"
                />
            </v-flex>
            <v-flex
                md12
                lg12
            >
                <SimpleTable :color="$store.state.app.color"
                             :title="`Most Popular Articles`"
                             :sub-title="`Last Updated at ${new Date().toDateString()}`"/>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
// Plugins
import '@/plugins/chartist'
import MaterialCard from '@/components/material/Card'
import MaterialChartCard from '@/components/material/ChartCard'
import MaterialStatsCard from '@/components/material/StatsCard'
import SimpleTable from '@/components/general/SimpleTable'
import Api from "@/api/resources/article";
import websiteApi from "@/api/resources/website";

export default {
    name: 'Dashboard',
    components: {
        MaterialCard, MaterialChartCard, MaterialStatsCard, SimpleTable
    },
     data() {
        return {
            articleCountInLastDay: 0,
            allArticleCount: 0,
            visitors: 0,
            visitorsInLastWeek: 0,
            totalVisitsInLastDay: 0,
            totalVisitsAllTime: 0,
            categories: 0,
            dailyData: [],
        }
    },

    methods: {
        complete(index) {
            this.list[index] = !this.list[index]
        },
        getCount(){
            this.loading = true;
            Api.ArticleCount().then(res=>{
                this.articleCountInLastDay=res.data.countInLastDay.original.data;
                this.allArticleCount=res.data.all.original.data.total;
                this.categories=res.data.categoryCount.original.data;
                this.dailyData=res.data.hitsPerDayLastWeek.original.data;
            })
            websiteApi.getVisits().then(res=>{
                this.totalVisitsAllTime=res.data.total
                this.totalVisitsInLastDay=res.data.lastDay;
            })

            websiteApi.getVisitors().then(res=>{
                this.visitors=res.data.allVisitor
                this.visitorsInLastWeek=res.data.visitorsLastWeek;
                this.loading=false;
            })

            this.loading=false;
        },
    },

    async created() {
        await this.getCount();
    }
}
</script>

<template>
    <v-toolbar id="core-toolbar" flat elevation="5">
        <v-app-bar-nav-icon :color="$store.state.app.color" v-if="responsive"
                            @click.stop="onClickBtn"></v-app-bar-nav-icon>

        <v-toolbar-title :class="`font-color-${$store.state.app.color}`">
            {{ pageTitle }}
        </v-toolbar-title>

        <v-spacer/>
        <v-toolbar-items>
            <v-flex
                align-center
                layout
                py-2>
                <router-link
                    v-ripple
                    class="toolbar-items"
                    :to="{ name: 'user-profile' }">
                    <v-icon>mdi-account</v-icon>
                    <v-card-text style="color: #19b275">{{user}}</v-card-text>
                </router-link>
                <v-icon
                    class="toolbar-items"
                    @click="logout">mdi-power
                </v-icon>
            </v-flex>
        </v-toolbar-items>
    </v-toolbar>
</template>

<script>
import {mapGetters, mapMutations} from 'vuex'
import authApi from "../../api/auth";

export default {
    data: () => ({
        user:'',
        notifications: [
            'Mike, Thanos is coming',
            '5 new avengers joined the team',
            'You\'re now friends with Capt',
            'Another Notification',
            'Another One - Dj Khalid voice'
        ],
        pageTitle: '',
        responsive: false,
        responsiveInput: false
    }),

    computed: {
        ...mapGetters(['authorized'])
    },

    watch: {
        $route(val) {
            this.pageTitle = this.$t('Core.Nav.' + val.meta.slug)
        }
    },

    mounted() {
        this.onResponsiveInverted()
        window.addEventListener('resize', this.onResponsiveInverted)
        this.pageTitle = this.$t('Core.Nav.' + this.$route.meta.slug)
        this.getUSer()
    },
    beforeDestroy() {
        window.removeEventListener('resize', this.onResponsiveInverted)
    },

    methods: {
        ...mapMutations('app', ['setDrawer', 'toggleDrawer']),
        onClickBtn() {
            this.toggleDrawer()
        },
        onClick() {
            //
        },
        async getUSer() {
            authApi.user().then(res => {
                this.user=res.data?.name
            })
        },
        onResponsiveInverted() {
            if (window.innerWidth < 991) {
                this.responsive = true
                this.responsiveInput = false
            } else {
                this.responsive = false
                this.responsiveInput = true
            }
        },
        logout: async function () {
            try {
                await this.$store.dispatch('logout');
            } catch ($e) {
                console.log($e)
            }
            await this.$router.push('/dashboard')
        }
    }
}
</script>

<style>
#core-toolbar a {
    text-decoration: none;
}
</style>

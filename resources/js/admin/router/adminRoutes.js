const routes = [
    {
        path: 'home',
        meta: {
            name: 'Dashboard',
            slug: 'dashboard',
            requiresAuth: true
        },
        component: () => import(`@/components/dash-views/Dashboard.vue`)
    },
    {
        path: 'user-profile',
        name: 'user-profile',
        meta: {
            name: 'User Profile',
            slug: 'user-profile',
            requiresAuth: true
        },
        component: () => import(`@/views/UserProfile.vue`)
    },
    {
        path: 'articles',
        name: 'articles',
        meta: {name: 'Articles', slug: 'articles', requiresAuth: true},
        component: () => import(`@/views/Articles/List.vue`)
    },
    {
        path: 'create-article',
        name: 'new-article',
        meta: {name: 'New Article', slug: 'new-article', requiresAuth: true},
        component: () => import(`@/views/Articles/New.vue`),
    },
    {
        path: 'articles/:slug/edit',
        name: 'edit-article',
        meta: {name: 'Edit Article', slug: 'edit-article', requiresAuth: true},
        component: () => import(`@/views/Articles/Edit.vue`),
    },
    {
        path: 'categories',
        name: 'categories',
        meta: {name: 'Categories', slug: 'categories', requiresAuth: true},
        component: () => import(`@/views/Categories/List.vue`),
    },
    {
        path: 'about-me',
        name: 'about-me',
        meta: {name: 'About Me', slug: 'about-me', requiresAuth: true},
        component: () => import(`@/views/AboutMe/List.vue`),
    },
    {
        path: 'workplace',
        name: 'workplace',
        meta: {name: 'Workplace', slug: 'workplace', requiresAuth: true},
        component: () => import(`@/views/Workplace/List.vue`)
    },
    {
        path: 'create-workplace',
        name: 'new-workplace',
        meta: {name: 'New Workplace', slug: 'new-workplace', requiresAuth: true},
        component: () => import(`@/views/Workplace/New.vue`),
    },
    {
        path: 'workplace/:slug/edit',
        name: 'edit-workplace',
        meta: {name: 'Edit workplace', slug: 'edit-workplace', requiresAuth: true},
        component: () => import(`@/views/Workplace/Edit.vue`),
    },
    {
        path: 'education',
        name: 'education',
        meta: {name: 'Education', slug: 'education', requiresAuth: true},
        component: () => import(`@/views/Education/List.vue`)
    },
    {
        path: 'create-education',
        name: 'new-education',
        meta: {name: 'New Education', slug: 'new-education', requiresAuth: true},
        component: () => import(`@/views/Education/New.vue`),
    },
    {
        path: 'education/:slug/edit',
        name: 'edit-education',
        meta: {name: 'Edit education', slug: 'edit-education', requiresAuth: true},
        component: () => import(`@/views/Education/Edit.vue`),
    },
    {
        path: 'projects',
        name: 'projects',
        meta: {name: 'Projects', slug: 'projects', requiresAuth: true},
        component: () => import(`@/views/Projects/List.vue`)
    },
    {
        path: 'create-project',
        name: 'new-project',
        meta: {name: 'New Project', slug: 'new-project', requiresAuth: true},
        component: () => import(`@/views/Projects/New.vue`),
    },
    {
        path: 'projects/:slug/edit',
        name: 'edit-projects',
        meta: {name: 'Edit Projects', slug: 'edit-projects', requiresAuth: true},
        component: () => import(`@/views/Projects/Edit.vue`),
    },
    {
        path: 'services',
        name: 'services',
        meta: {name: 'Services', slug: 'services', requiresAuth: true},
        component: () => import(`@/views/Services/List.vue`)
    },
    {
        path: 'skills',
        name: 'skills',
        meta: {name: 'Skills', slug: 'skills', requiresAuth: true},
        component: () => import(`@/views/Skills/List.vue`)
    },
    {
        path: 'system-settings',
        name: 'system-settings',
        meta: {name: 'System Settings', slug: 'system-settings', requiresAuth: true},
        component: () => import(`@/views/Settings/SystemSettings.vue`),
    }
]

export default routes

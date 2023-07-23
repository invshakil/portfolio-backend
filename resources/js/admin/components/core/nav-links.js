const adminPrefixSlug = 'dashboard/admin'
const adminRoutes = [
    {
        to: `/${adminPrefixSlug}/home`,
        icon: 'mdi-view-dashboard',
        slug: 'dashboard'
    },
    {
        to: `#`,
        icon: 'mdi-view-list',
        slug: 'articles',
        subLinks: [
            {
                to: `/${adminPrefixSlug}/categories`,
                icon: 'mdi-tag-multiple',
                slug: 'categories'
            },
            {
                to: `/${adminPrefixSlug}/articles`,
                icon: 'mdi-table-edit',
                slug: 'articles'
            },
            {
                to: `/${adminPrefixSlug}/create-article`,
                icon: 'mdi-square-edit-outline',
                slug: 'new-article'
            }
        ]
    },
    {
        to: `#`,
        icon: 'mdi-facebook-workplace',
        slug: 'workplace',
        subLinks: [
            {
                to: `/${adminPrefixSlug}/workplace`,
                icon: 'mdi-table-edit',
                slug: 'workplace'
            },
            {
                to: `/${adminPrefixSlug}/create-workplace`,
                icon: 'mdi-square-edit-outline',
                slug: 'new-workplace'
            }
        ]
    },
    {
        to: `#`,
        icon: 'mdi-school',
        slug: 'education',
        subLinks: [
            {
                to: `/${adminPrefixSlug}/education`,
                icon: 'mdi-table-edit',
                slug: 'education'
            },
            {
                to: `/${adminPrefixSlug}/create-education`,
                icon: 'mdi-square-edit-outline',
                slug: 'new-education'
            }
        ]
    },
    {
        to: `#`,
        icon: 'mdi-briefcase-edit',
        slug: 'projects',
        subLinks: [
            {
                to: `/${adminPrefixSlug}/projects`,
                icon: 'mdi-table-edit',
                slug: 'projects'
            },
            {
                to: `/${adminPrefixSlug}/create-project`,
                icon: 'mdi-square-edit-outline',
                slug: 'new-project'
            }
        ]
    },
    {
        to: `/${adminPrefixSlug}/interests`,
        icon: 'mdi-handshake-outline',
        slug: 'interests',
    },
    {
        to: `/${adminPrefixSlug}/news`,
        icon: 'mdi-square-edit-outline',
        slug: 'news'
    },
    {
        to: `/${adminPrefixSlug}/skills`,
        icon: 'mdi-car-esp',
        slug: 'skills',
    },
    {
        to: `/${adminPrefixSlug}/about-me`,
        icon: 'mdi-information-outline',
        slug: 'about-me',
    },
    {
        to: `#`,
        icon: 'mdi-cog',
        slug: 'settings',
        subLinks: [
            {
                to: `/${adminPrefixSlug}/system-settings`,
                icon: 'mdi-cog',
                slug: 'system-settings',
            },
            {
                to: `/${adminPrefixSlug}/user-profile`,
                icon: 'mdi-account',
                slug: 'user-profile'
            },
        ]
    }
]

export default adminRoutes

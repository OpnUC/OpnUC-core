import Template from './components/Template.vue'
import Index from './components/Index.vue'
import Sidebar from './components/Sidebar.vue'
import Login from './components/Login.vue'
import NotFoundView from './components/404.vue'

// Routes
const routes = [
    {
        path: '/',
        component: Template,
        children: [
            {
                path: '',
                components: {
                    sidebar: Sidebar,
                    default: Index
                },
                name: 'Dashboard',
                meta: {
                    description: 'Overview of environment'
                }
            },
            {
                path: '/Login',
                component: Login,
                name: 'Login',
                meta: {
                    description: 'Overview of environment'
                }
            },
            {
                path: '*',
                component: NotFoundView,
                name: 'NotFound',
                meta: {
                    description: '404 Not Found'
                }
            },
        ],
    },

]

export default routes

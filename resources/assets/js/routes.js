import Template from './components/Template.vue'
import Index from './components/Index.vue'
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
                component: Index,
                name: 'Dashboard',
                meta: {description: 'Overview of environment'}
            },
            {
                path: 'Login',
                component: Login,
                name: 'Login',
                meta: {
                    description: 'Overview of environment',
                    sidebar:false
                }
            },
            {
                path: '*',
                component: NotFoundView,
                name: 'NotFound',
                meta: {description: '404 Not Found'}
            },
        ],
    },
]

export default routes

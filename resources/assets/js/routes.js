import Template from './components/Template.vue'

import Index from './components/Index.vue'
import Sidebar from './components/Sidebar.vue'

import Cdr from './components/Cdr.vue'

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
                name: 'Home',
                meta: {
                    description: 'Home',
                }
            },
            {
                path: '/Cdr',
                component: Cdr,
                name: '発着信履歴',
                meta: {
                    description: 'Call detail record',
                    auth: true
                }
            },
            // {
            //     path: '',
            //     components: {
            //         sidebar: Sidebar,
            //         default: Index
            //     },
            //     name: 'Dashboard',
            //     meta: {
            //         description: 'Overview of environment'
            //     }
            // },
            {
                path: '/Login',
                component: Login,
                name: 'Login',
                meta: {
                    description: 'Login',
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

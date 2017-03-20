import Template from './components/Template.vue'

import Index from './components/Index.vue'

import Cdr from './components/Cdr.vue'
import AddressBook_Template from './components/AddressBook_Template.vue'
import AddressBook from './components/AddressBook.vue'
import AddressBook_Sidebar from './components/AddressBook_Sidebar.vue'
import AddressBookEdit from './components/AddressBookEdit.vue'

import Login from './components/Login.vue'
import PasswordReset from './components/PasswordReset.vue'
import PasswordResetEmail from './components/PasswordResetEmail.vue'

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
                meta: {
                    title: 'Home',
                    description: 'Home',
                }
            },
            {
                path: '/Cdr',
                component: Cdr,
                meta: {
                    title: '発着信履歴',
                    description: 'Call detail record',
                    auth: true
                }
            },
            {
                path: '/AddressBook',
                components: {
                    default: AddressBook_Template,
                    sidebar: AddressBook_Sidebar
                },
                children: [
                    {
                        path: '',
                        name: 'AddressBook',
                        component: AddressBook,
                        meta: {
                            title: 'Web電話帳',
                            description: 'Web Address Book',
                            auth: true
                        },
                    },
                    {
                        path: 'Edit/:id?',
                        name: 'AddressBookEdit',
                        component: AddressBookEdit,
                        meta: {
                            title: 'Web電話帳',
                            description: 'Web Address Book',
                            auth: true
                        },
                    },
                ],
            },
            {
                path: '/Login',
                component: Login,
                meta: {
                    title: 'Login',
                    description: 'Login',
                }
            },
            {
                path: '/PasswordReset/:token',
                component: PasswordReset,
                meta: {
                    title: 'パスワードリセット',
                    description: 'Password Reset',
                }
            },
            {
                path: '/PasswordResetEmail',
                component: PasswordResetEmail,
                meta: {
                    title: 'パスワードリセット',
                    description: 'Password Reset',
                }
            },
            {
                path: '*',
                component: NotFoundView,
                meta: {
                    title: 'NotFound',
                    description: '404 Not Found'
                }
            },
        ],
    },

]

export default routes

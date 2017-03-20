import Template from './components/Template.vue'

import Index from './components/Index.vue'

import Cdr from './components/Cdr.vue'
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
                name: 'AddressBook',
                components: {
                    default: AddressBook,
                    sidebar: AddressBook_Sidebar
                },
                meta: {
                    title: 'Web電話帳',
                    description: 'Web Address Book',
                    auth: true
                },
            },
            {
                path: '/AddressBook/Edit/:id?',
                name: 'AddressBookEdit',
                components: {
                    default: AddressBookEdit,
                    sidebar: AddressBook_Sidebar
                },
                meta: {
                    title: 'Web電話帳',
                    description: 'Web Address Book',
                    auth: true
                },
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

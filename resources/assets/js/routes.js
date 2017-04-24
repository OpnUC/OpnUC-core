import Template from './components/Template.vue'

import Index from './components/Index.vue'

import Cdr from './components/Cdr.vue'
import AddressBook_Template from './components/AddressBook_Template.vue'
import AddressBook from './components/AddressBook.vue'
import AddressBook_Sidebar from './components/AddressBook_Sidebar.vue'
import AddressBookEdit from './components/AddressBookEdit.vue'
import AddressBookGroup from './components/AddressBookGroup.vue'
import AddressBookGroupEdit from './components/AddressBookGroupEdit.vue'

import User_Template from './components/User_Template.vue'
import User_Sidebar from './components/User_Sidebar.vue'
import User from './components/User.vue'
import User_PasswordChange from './components/User_PasswordChange.vue'
import User_AddressBook from './components/User_AddressBook.vue'

import Admin_Template from './components/Admin_Template.vue'
import Admin_Sidebar from './components/Admin_Sidebar.vue'
import Admin from './components/Admin.vue'
import Admin_Users from './components/Admin_Users.vue'
import Admin_UserEdit from './components/Admin_UserEdit.vue'

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
                        path: 'Group',
                        name: 'AddressBookGroup',
                        component: AddressBookGroup,
                        meta: {
                            title: 'Web電話帳',
                            description: 'Web Address Book',
                            auth: true
                        },
                    },
                    {
                        path: 'GroupEdit/:id?',
                        name: 'AddressBookGroupEdit',
                        component: AddressBookGroupEdit,
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
                path: '/User',
                components: {
                    default: User_Template,
                    sidebar: User_Sidebar
                },
                children: [
                    {
                        path: '',
                        name: 'User',
                        component: User,
                        meta: {
                            title: 'ユーザ情報',
                            description: 'User',
                            auth: true
                        },
                    },
                    {
                        path: 'PasswordChange',
                        name: 'UserPasswordChange',
                        component: User_PasswordChange,
                        meta: {
                            title: 'ユーザ情報',
                            description: 'User',
                            auth: true
                        },
                    },
                    {
                        path: 'AddressBook',
                        name: 'UserAddressBook',
                        component: User_AddressBook,
                        meta: {
                            title: 'ユーザ情報',
                            description: 'User',
                            auth: true
                        },
                    },
                ]
            },
            {
                path: '/Admin',
                components: {
                    default: Admin_Template,
                    sidebar: Admin_Sidebar
                },
                children: [
                    {
                        path: '',
                        name: 'Admin',
                        component: Admin,
                        meta: {
                            title: 'システム管理',
                            description: 'System Administration',
                            auth: 'system-admin'
                        },
                    },
                    {
                        path: 'Users',
                        name: 'Admin_Users',
                        component: Admin_Users,
                        meta: {
                            title: 'システム管理',
                            description: 'System Administration',
                            auth: 'system-admin'
                        },
                    },
                    {
                        path: 'UserEdit/:id?',
                        name: 'AdminUserEdit',
                        component: Admin_UserEdit,
                        meta: {
                            title: 'システム管理',
                            description: 'System Administration',
                            auth: 'system-admin'
                        },
                    },
                ]
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

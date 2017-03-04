import Vue from './vue.es6';
import {router} from './vue.es6';
import axios from 'axios';

export default {
    user: {
        authenticated: false,
        profile: null
    },
    check() {
        if (localStorage.getItem('id_token') !== null) {
            axios.get(
                '/authenticate',
            ).then(response => {
                this.user.authenticated = true
                this.user.profile = response.data.profile
            })
        }
    },
    signin(context, username, password) {
        axios.post(
            '/authenticate',
            {
                username: username,
                password: password
            }
        ).then(response => {
            context.error = false
            localStorage.setItem('id_token', response.data.token)

            this.user.authenticated = true
            this.user.profile = response.data.profile

            router.push('/')
        }, response => {
            context.error = true
        })
    },
    signout() {
        localStorage.removeItem('id_token')
        this.user.authenticated = false
        this.user.profile = null

        router.push('/')
    }
    // register(context, name, email, password) {
    //     Vue.http.post(
    //         'api/register',
    //         {
    //             name: name,
    //             email: email,
    //             password: password
    //         }
    //     ).then(response => {
    //         context.success = true
    //     }, response => {
    //         context.response = response.data
    //         context.error = true
    //     })
    // }
}
window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

window.$ = window.jQuery = require('jquery');

require('bootstrap-sass');
require('admin-lte');
/**
 * Vue is a modern JavaScript library for building interactive web interfaces
 * using reactive data binding and reusable components. Vue's API is clean
 * and simple, leaving you to focus on building your next great project.
 */

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common = {
    'X-CSRF-TOKEN': window.Laravel.csrfToken,
    'X-Requested-With': 'XMLHttpRequest'
};

import Echo from "laravel-echo"

window.echo = new Echo({
    broadcaster: 'socket.io',
    host: window.location.hostname + ':6001',
    auth: {
        headers: {
            'Authorization': 'Bearer ' + localStorage.getItem('default-auth-token')
        }
    }
});

window.extStatus = {
    'unknown': {
        'statusClass': 'fa fa-circle text-gray',
        'statusText': '不明'
    },
    'idle': {
        'statusClass': 'fa fa-circle text-info',
        'statusText': 'アイドル'
    },
    'away': {
        'statusClass': 'fa fa-circle text-primary',
        'statusText': '不在'
    },
    'busy': {
        'statusClass': 'fa fa-circle text-danger',
        'statusText': '通話中'
    },
};

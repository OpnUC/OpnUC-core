let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

var paths = {
    'admin_lte': 'node_modules/admin-lte',
    'fontawesome': 'node_modules/font-awesome'
}

mix.js([
        'resources/assets/js/app.js',
        'resources/assets/js/vue.es6'
    ]
    , 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css')
    .combine([
        // Admin LTE
        paths.admin_lte + '/dist/css/AdminLTE.min.css',
        paths.admin_lte + '/dist/css/alt/AdminLTE-select2.min.css',
        paths.admin_lte + '/dist/css/skins/skin-blue-light.min.css',
        // Font Awsome
        paths.fontawesome + '/css/font-awesome.css'
    ], 'public/css/vendor.css')
    .extract(['libphonenumber-js', 'element-ui', 'bootstrap-sass', 'axios', 'jquery', 'vue', 'vue-router', 'vue-events', 'vue-axios', 'moment', 'encoding-japanese'])
    .copy(
        'node_modules/font-awesome/fonts',
        'public/fonts'
    );

if (mix.config.inProduction) {
    mix.version();
}
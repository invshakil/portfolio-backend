const mix = require('laravel-mix');
const path = require('path');
let config = require('./webpack.config');

mix.js('resources/js/app.js', 'public/js/admin/index.js')
    .vue()
    .version();

mix.sass('resources/sass/admin/index.scss', 'public/css/admin.css')
    .styles(['public/assets/css/bootstrap.min.css', 'public/assets/css/animate.css', 'public/assets/css/material-icons.css'], 'public/css/app.css')
    .copyDirectory('resources/assets/', 'public/')
    .version();

mix.webpackConfig(config);

mix.options({
    processCssUrls: false,
    postCss: [],
    terser: {},
    autoprefixer: {},
    legacyNodePolyfills: false
});

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

// mix.js('resources/assets/js/common.js', 'public/js')
//    .sass('resources/assets/sass/main.sass', 'public/css');

//сборка js и css файлов сайта
mix
    .js('resources/assets/js/app.js', 'public/js')
    .sass('resources/assets/scss/app.sass', 'public/css');

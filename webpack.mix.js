let mix = require('laravel-mix');
require('laravel-mix-eslint-config');
require('laravel-mix-stylelint');

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

mix.js('resources/js/app.js', 'public/js')
    .stylelint({ files: ['resources/**/*.s?(a|c)ss']})
    .eslint({
        enforce: 'pre',
        test: /\.js$/,
        exclude: ['node_modules', 'containers'],
        loader: 'eslint-loader',
        options: {
            fix: false,
            cache: false,
        }
    })
    .sass('resources/sass/app.scss', 'public/css')
    .extract();

mix.copyDirectory('resources/images', 'public/images');

if (!mix.inProduction()) {
    mix.webpackConfig({
        devtool: 'source-map'
    })
    .sourceMaps();
} else {
    mix.version();
    mix.disableNotifications();
}

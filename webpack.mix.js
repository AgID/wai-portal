let mix = require('laravel-mix');
let StyleLintPlugin = require('stylelint-webpack-plugin');

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

mix.js('resources/assets/js/app.js', 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css');

mix.webpackConfig({
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: [/node_modules/, /containers/],
                loader: 'eslint-loader',
            }
        ]
    },
    plugins: [
        new StyleLintPlugin({
            files: ['resources/**/*.s?(a|c)ss']
        }),
    ]
});

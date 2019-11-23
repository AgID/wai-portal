const mix = require('laravel-mix');
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

mix.webpackConfig({
    output: {
        chunkFilename: 'js/chunks/[name].js',
    }
});

mix.autoload({
    jquery: ['$', 'jQuery']
});

mix.js('resources/js/app.js', 'public/js')
    .stylelint({ files: ['**/*.s?(a|c)ss']})
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
    .sass('resources/sass/datatables.scss', 'public/css');
    // Do not use automatic vendor extraction in this version.
    // See https://github.com/JeffreyWay/laravel-mix/issues/1914
    // .extract();

mix.copyDirectory('resources/images', 'public/images');
mix.copyDirectory('node_modules/bootstrap-italia/dist/fonts', 'public/fonts');
mix.copyDirectory('node_modules/bootstrap-italia/dist/svg', 'public/svg');

if (!mix.inProduction()) {
    mix.webpackConfig({
        devtool: 'source-map'
    })
    .sourceMaps();
} else {
    mix.version();
    mix.disableNotifications();
}

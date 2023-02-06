const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js').sourceMaps();

/*mix.browserSync({
    proxy: 'http://rgp.test',
    //browser: 'google chrome',
    open: 'false'
});*/

if (mix.inProduction()) {
    mix.version();
}

mix.webpackConfig({
    output: {
        chunkFilename: mix.inProduction() ? 'js/build/[chunkhash].js' : 'js/chunks/[name].js',
    }
});

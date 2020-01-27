const mix = require('laravel-mix');

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

mix.webpackConfig( {
    module: {
	rules: [{
	    test: /\.elm$/,
	    exclude: [/elm-stuff/, /node_modules/],
	    use: {
		loader: 'elm-webpack-loader',
            options: {
                debug: true
            },
	    }
	}]
    }
})
    .js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css');

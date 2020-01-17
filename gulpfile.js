
const composer = require('gulp-composer');
const del = require('del');
const gulp = require('gulp');
const webpack = require('webpack-stream');
const zip = require('gulp-zip')

function scripts() {
	return gulp.src('.')
		.pipe(webpack(require('./webpack.config.js')))
		.pipe(gulp.dest('js/'));
}

function compress() {
	return gulp.src([
		'app/**',
		'js/**',
		'vendor/**',
		'PLUGIN_HELP.md',
		'wordpress-custom-directory.php'
	], { base: '.' })
		.pipe(zip('wordpress-custom-directory.zip'))
		.pipe(gulp.dest('./'))
}

function php() {
	if (process.env.NODE_ENV == 'production') {
		composer('install --no-dev');
		return composer('dump-autoload -o')
	} else {
		composer('install');
		return composer('dump-autoload')
	}
}

function clean() {
	composer('install');
	composer('dump-autoload');
	return del(['js/', 'css/', '*.zip'])
}

exports.scripts = scripts;
exports.build = gulp.series(clean, scripts, php);
exports.compress = gulp.series(scripts, php, compress);
exports.clean = clean;

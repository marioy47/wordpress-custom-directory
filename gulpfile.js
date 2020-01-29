'use strict';

const composer = require('gulp-composer');
const del = require('del');
const gulp = require('gulp');
const webpack = require('webpack-stream');
const wpPot = require('gulp-wp-pot');
const zip = require('gulp-zip');

/**
 * Compiles and bundles JavaScript using WebPack.
 */
function scripts() {
	const webpackConfig = require('./webpack.config.js');
	webpackConfig.watch = process.env.NODE_ENV == 'production' ? false : true;
	return gulp.src('.')
		.pipe(webpack(webpackConfig))
		.pipe(gulp.dest('js/'));
}

/**
 * Creates a zip file of the plugin.
 */
function compress() {
	return gulp.src([
		'classes/**',
		'help/**',
		'js/**',
		'languages/*',
		'PLUGIN_HELP.md',
		'vendor/**',
		'wordpress-custom-directory.php'
	], { base: '../' })
		.pipe(zip('wordpress-custom-directory.zip'))
		.pipe(gulp.dest('./'))
}

/**
 * Executes composer on prod or dev dpending on the NODE_ENV status.
 */
function composerInstall() {
	if (process.env.NODE_ENV == 'production') {
		composer('install --no-dev');
		return composer('dump-autoload -o')
	} else {
		composer('install');
		return composer('dump-autoload')
	}
}

/**
 * Removes compiled files and any cache that exits.
 */
function clean() {
	composer('install');
	composer('dump-autoload');
	return del(['js/', 'css/', '*.zip'])
}

function potCreate() {
	return gulp.src(['wordpress-custom-directory.php', 'classes/*.php'])
		.pipe(wpPot({
			domain: 'wp-custom-dir',
			package: 'Wordpress_Custom_Dir'
		}))
		.pipe(gulp.dest('languages/wp-custom-dir.pot'));
}


/**
 * Exportes tasks.
 */
exports.build = gulp.series(clean, scripts, potCreate, php);
exports.clean = clean;
exports.compress = gulp.series(clean, scripts, potCreate, php, compress);
exports.php = composerInstall;
exports.pot = gulp.series(potCreate);
exports.scripts = scripts;
exports.watch = gulp.series(scripts);
exports.zip = compress;

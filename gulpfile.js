'use strict';

const composer = require('gulp-composer');
const del = require('del');
const gulp = require('gulp');
const path = require('path');
const webpack = require('webpack-stream');
const wpPot = require('gulp-wp-pot');
const zip = require('gulp-zip');

/**
 * Compiles and bundles JavaScript using WebPack.
 */
function scripts() {
	const webpackConfig = require('./webpack.config.js');
	webpackConfig.watch = process.env.NODE_ENV === 'production' ? false : true;
	return gulp.src('.').pipe(webpack(webpackConfig)).pipe(gulp.dest('js/'));
}

/**
 * Executes composer on prod or dev dpending on the NODE_ENV status.
 */
function phpComposer() {
	if (process.env.NODE_ENV === 'production') {
		composer('install --no-dev', { async: false });
		return composer('dump-autoload -o', { async: false });
	}

	composer('install', { async: false });
	return composer('dump-autoload', { async: false });
}

/**
 * Extract the strings from the PHP files and create the pot file in languages/
 */
function potCreate() {
	return gulp
		.src(['wordpress-custom-directory.php', 'classes/*.php'])
		.pipe(
			wpPot({
				domain: 'wp-custom-dir',
				package: 'Wordpress_Custom_Dir',
			})
		)
		.pipe(gulp.dest('languages/wp-custom-dir.pot'));
}

/**
 * Removes compiled files and any cache that exits.
 */
function clean() {
	composer('install');
	composer('dump-autoload');
	return del(['js/', 'css/', '*.zip']);
}

/**
 * Creates a zip file of the plugin.
 */
function compress() {
	const filename = path.basename(__dirname) + '.zip';
	return gulp
		.src(['classes/**', 'help/**', 'js/**', 'languages/*', 'vendor/**', '*.php'], { base: '../' })
		.pipe(zip(filename))
		.pipe(gulp.dest('./'));
}

/**
 * Exportes tasks.
 */
exports.compress = compress;
exports.clean = clean;

exports.build = gulp.series(clean, scripts, potCreate, phpComposer);
exports.watch = gulp.series(phpComposer, scripts);
exports.zip = gulp.series(clean, scripts, potCreate, phpComposer, compress);

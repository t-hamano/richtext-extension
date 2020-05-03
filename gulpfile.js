const gulp = require( 'gulp' );
const gulpZip = require( 'gulp-zip' );
const del = require( 'del' );

function cleanFiles( cb ) {
	return del(
		[
			'./richtext-extension'
		],
		cb );
}

function copyFiles() {
	return gulp.src(
		[
			'**',
			'!.gitignore',
			'!node_modules',
			'!node_modules/**',
			'!gulpfile.js',
			'!package.json',
			'!package-lock.json',
			'!webpack.config.js',
			'!src',
			'!src/**',
			'!phpcs.ruleset.xml'
		],
		{ base: './' }
	)
		.pipe( gulp.dest( './richtext-extension' ) );
}

function zip() {
	return gulp.src( 'richtext-extension/**', { base: '.' })
		.pipe( gulpZip( 'richtext-extension.zip' ) )
		.pipe( gulp.dest( 'release' ) );
}

exports.default = gulp.series( copyFiles, zip, cleanFiles );

module.exports = {
	extends: [
		'@wordpress/stylelint-config/scss',
	],
  ignoreFiles: [
		'build/**/*.css',
		'node_modules/**/*.css',
		'vendor/**/*.css',
		'**/*.js',
		'**/*.svg',
		'.stylelintrc.js'
  ],
	rules: {
		'no-descending-specificity': null,
	}
}

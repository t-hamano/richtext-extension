module.exports = {
	extends: [
		"stylelint-config-wordpress/scss",
		"stylelint-config-rational-order",
	],
  ignoreFiles: [
		"node_modules/**/*.css",
		"css/**/*.css",
  ],
	rules: {
		'no-descending-specificity': null,
		'font-weight-notation': null,
		'font-family-no-missing-generic-family-keyword': null,
		"selector-class-pattern": null,
	}
}

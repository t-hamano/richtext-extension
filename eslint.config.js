/**
 * WordPress dependencies
 */
const defaultConfig = require( '@wordpress/eslint-plugin' );

module.exports = [
	{
		ignores: [ '**/node_modules/**', '**/vendor/**', '**/build/**' ],
	},
	...defaultConfig.configs.recommended,
	{
		languageOptions: {
			globals: {
				rtexConf: 'readonly',
			},
		},
		rules: {
			'react/jsx-boolean-value': 'error',
			'import/no-unresolved': 'off',
			'import/no-extraneous-dependencies': 'off',
			'react/jsx-curly-brace-presence': [ 'error', { props: 'never', children: 'never' } ],
			'@wordpress/dependency-group': 'error',
			'@wordpress/i18n-text-domain': [
				'error',
				{
					allowedTextDomain: 'richtext-extension',
				},
			],
		},
	},
];

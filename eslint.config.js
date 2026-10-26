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
		linterOptions: {
			reportUnusedDisableDirectives: 'error',
		},
		languageOptions: {
			globals: {
				rtexConf: 'readonly',
			},
		},
		rules: {
			'react/jsx-boolean-value': 'error',
			'import/no-extraneous-dependencies': 'off',
			'react/jsx-curly-brace-presence': [ 'error', { props: 'never', children: 'never' } ],
			'jsdoc/escape-inline-tags': 'error',
			'@wordpress/wp-global-usage': 'error',
			'@wordpress/data-no-store-string-literals': 'error',
			'@wordpress/components-no-unsafe-button-disabled': 'error',
			'@wordpress/components-no-missing-40px-size-prop': 'error',
			'@wordpress/react-no-unsafe-timeout': 'error',
			'@wordpress/use-recommended-components': [ 'error', { allowUseWithCaution: true } ],
			'@wordpress/dependency-group': 'error',
			'@wordpress/i18n-text-domain': [
				'error',
				{
					allowedTextDomain: 'richtext-extension',
				},
			],
		},
	},
	{
		files: [ '**/*.ts', '**/*.tsx' ],
		rules: {
			'@typescript-eslint/ban-ts-comment': [
				'error',
				{
					'ts-expect-error': 'allow-with-description',
					'ts-ignore': true,
					'ts-nocheck': true,
					'ts-check': false,
				},
			],
			'@typescript-eslint/consistent-type-imports': [
				'error',
				{
					prefer: 'type-imports',
					disallowTypeAnnotations: false,
				},
			],
			'@typescript-eslint/no-restricted-imports': [
				'error',
				{
					paths: [
						{
							name: 'react',
							message: 'Please use React API through `@wordpress/element` instead.',
							allowTypeImports: true,
						},
					],
				},
			],
		},
	},
];

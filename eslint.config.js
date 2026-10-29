/**
 * External dependencies
 */
const reactHooksPlugin = require( 'eslint-plugin-react-hooks' );

/**
 * WordPress dependencies
 */
const defaultConfig = require( '@wordpress/eslint-plugin' );

module.exports = [
	{
		ignores: [ '**/node_modules/**', '**/vendor/**', '**/build/**' ],
	},
	// React Compiler rules. `@wordpress/eslint-plugin` overrides the classic hooks rules below.
	reactHooksPlugin.configs.flat[ 'recommended-latest' ],
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
			'react-hooks/config': [
				'error',
				{
					environment: {
						enableTreatRefLikeIdentifiersAsRefs: true,
						validateRefAccessDuringRender: false,
					},
				},
			],
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
	{
		files: [ 'src/**' ],
		rules: {
			'import/no-extraneous-dependencies': 'error',
		},
	},
	...defaultConfig.configs[ 'test-playwright' ].map( ( config ) => ( {
		...config,
		files: [ 'tests/e2e/**/*.ts' ],
	} ) ),
];

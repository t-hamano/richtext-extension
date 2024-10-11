module.exports = {
	extends: [ 'plugin:@wordpress/eslint-plugin/recommended' ],
	parser: '@babel/eslint-parser',
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
		'prettier/prettier': [
			'error',
			{
				useTabs: true,
				tabWidth: 2,
				singleQuote: true,
				printWidth: 100,
				bracketSpacing: true,
				parenSpacing: true,
				bracketSameLine: false,
			},
		],
	},
	globals: {
		rtexConf: true,
	},
	parserOptions: {
		requireConfigFile: false,
		babelOptions: {
			presets: [ '@babel/preset-react' ],
		},
	},
};

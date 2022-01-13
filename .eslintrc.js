module.exports = {
	extends: [ 'plugin:@wordpress/eslint-plugin/recommended' ],
	parser: '@babel/eslint-parser',
	rules: {
		'import/no-unresolved': 'off',
		'import/no-extraneous-dependencies': 'off',
		'prettier/prettier': [
			'error',
			{
				useTabs: true,
				tabWidth: 2,
				singleQuote: true,
				printWidth: 100,
				bracketSpacing: true,
				parenSpacing: true,
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

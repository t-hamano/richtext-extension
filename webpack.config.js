const defaultConfig = require( './node_modules/@wordpress/scripts/config/webpack.config.js' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const IgnoreEmitPlugin = require( 'ignore-emit-webpack-plugin' );
const production = '' === process.env.NODE_ENV;

module.exports = {
	...defaultConfig,
  entry: {
    'js/format': './src/format/index.js',
		'css/style-editor': './src/sass/style-editor.scss',
		'css/style-option': './src/sass/style-option.scss',
  },

	output: {
    filename: '[name].js',
    path: __dirname
	},
  module: {
		...defaultConfig.module,
		rules: [
			...defaultConfig.module.rules,
			{
				test: /\.(sa|sc|c)ss$/,
				use: [
					MiniCssExtractPlugin.loader,
					{
						loader: 'css-loader',
						options: {
							sourceMap: ! production,
							url: false
						}
					},
					{
						loader: 'sass-loader',
						options: {
							sourceMap: ! production
						}
					}
				]
			}
	  ]
  },
  plugins: [
		...defaultConfig.plugins,
		new MiniCssExtractPlugin({
      filename: './[name].css',
    }),
		new IgnoreEmitPlugin([
			'style-editor.js',
			'style-editor.asset.php',
			'style-option.js',
			'style-option.asset.php',
		])
	]
};

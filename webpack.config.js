const path                   = require( 'path' );
const MiniCssExtractPlugin   = require( 'mini-css-extract-plugin' );
const { CleanWebpackPlugin } = require( 'clean-webpack-plugin' );
const TerserPlugin           = require( 'terser-webpack-plugin' );
const CssMinimizerPlugin     = require( 'css-minimizer-webpack-plugin' );
const autoprefixer           = require( 'autoprefixer' );

const isProduction = process.env.NODE_ENV === 'production';

module.exports = {
	mode: isProduction ? 'production' : 'development',
	devtool: "eval-source-map",
	entry: {
		core: './assets/js/ash.js',
		ash: path.resolve( __dirname, './assets/scss/ash.scss' )
	},
	output: {
		filename: '[name].js',
		path: path.resolve( __dirname, 'js' ),
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				use: {
					loader: 'babel-loader',
					options: {
						presets: ['@babel/preset-env'],
						compact: isProduction,
					},
				},
			},
			{
				test: /\.(woff2?|eot|ttf|otf|svg)$/,
				type: 'asset/resource',
				generator: {
					filename: '../fonts/[name][ext][query]',
				}
			},
			{
				test: /\.scss$/,
				include: [ path.resolve( __dirname, 'assets/scss/' ) ],
				use: [
					MiniCssExtractPlugin.loader,
					'css-loader',
					{
						loader: 'postcss-loader',
						options: {
							postcssOptions: {
								plugins: [autoprefixer()],
							},
						},
					},
					'resolve-url-loader',
					{
						loader: 'sass-loader',
						options: {
							implementation: require.resolve( 'sass' ),
							sourceMap: true,
						},
					},
				],
			},
		],
	},
	plugins: [
		new CleanWebpackPlugin({
			// Cleanup our folders
			cleanOnceBeforeBuildPatterns: [
				path.resolve( __dirname, 'js' ),
				path.resolve( __dirname, 'css' ),
				path.resolve( __dirname, 'fonts' ),
			],
			cleanAfterEveryBuildPatterns: [
				path.resolve( __dirname, 'js', 'ash.js' )
			]
		}),
		new MiniCssExtractPlugin( {
			filename: isProduction ? '../css/[name].css' : '../css/[name].css',
			chunkFilename: isProduction ? '../css/[id].css' : '../css/[id].css',
		} ),
	],
	optimization: {
		minimize: isProduction,
		minimizer: [
			new TerserPlugin( {
				extractComments: false,
			} ),
			new CssMinimizerPlugin(),
		],
	},
};
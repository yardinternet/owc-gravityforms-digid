const path = require( 'path' );

const babelRule = {
	test: /\.(js)$/,
	exclude: /node_modules/,
	use: {
		loader: 'babel-loader',
		options: {
			presets: [ '@babel/preset-env' ],
		},
	},
};

const countdownConfig = {
	entry: './resources/js/index.js',
	mode: process.env.NODE_ENV ? process.env.NODE_ENV : 'development',
	output: {
		library: 'CountdownDigiD',
		libraryTarget: 'umd',
		globalObject: '(typeof self !== "undefined" ? self : this)',
		libraryExport: 'default',
		path: path.resolve( __dirname, 'resources/js/dist' ),
		filename: 'owc-gf-digid.js',
		publicPath: '/',
	},
	module: {
		rules: [ babelRule ],
	},
	resolve: {
		extensions: [ '*', '.js' ],
	},
};

/*
 * The "DigiD login" Gutenberg block. Relies on `wp.*` globals (see
 * resources/blocks/digid-login/index.js) instead of `@wordpress/*` imports,
 * so a plain webpack build works and no @wordpress/scripts dependency is
 * needed just for this one block.
 */
const blockConfig = {
	entry: './resources/blocks/digid-login/index.js',
	mode: process.env.NODE_ENV ? process.env.NODE_ENV : 'development',
	output: {
		path: path.resolve( __dirname, 'resources/blocks/dist' ),
		filename: 'digid-login.js',
		publicPath: '/',
	},
	module: {
		rules: [ babelRule ],
	},
	resolve: {
		extensions: [ '*', '.js' ],
	},
};

module.exports = [ countdownConfig, blockConfig ];

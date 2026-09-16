const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

// Blocks are discovered from block.json; the frontend countdown script is not a block.
module.exports = {
	...defaultConfig,
	entry: {
		...defaultConfig.entry(),
		'owc-gf-digid': './resources/js/index.js',
	},
};

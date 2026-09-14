/**
 * Registers the "DigiD login" block.
 *
 * Rendered entirely server-side (see DigiDBlockServiceProvider::render()),
 * so the editor only needs a ServerSideRender preview and no `save` markup.
 * Relies on `wp.*` globals (wp-blocks/wp-element/wp-block-editor/
 * wp-components/wp-server-side-render) rather than `@wordpress/*` imports,
 * so it can be built with this plugin's existing plain webpack + babel-loader
 * setup instead of pulling in @wordpress/scripts.
 */
( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { createElement: el } = wp.element;
	const { useBlockProps } = wp.blockEditor;
	const { Disabled } = wp.components;
	const ServerSideRender = wp.serverSideRender;

	const metadata = require( './block.json' );

	registerBlockType( metadata.name, {
		edit() {
			return el(
				'div',
				useBlockProps(),
				el(
					Disabled,
					{},
					el( ServerSideRender, { block: metadata.name } )
				)
			);
		},
		save() {
			return null;
		},
	} );
} )( window.wp );

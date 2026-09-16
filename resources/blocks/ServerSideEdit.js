import { useBlockProps } from '@wordpress/block-editor';
import { Disabled } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

const ServerSideEdit = ( { name } ) => (
	<div { ...useBlockProps() }>
		<Disabled>
			<ServerSideRender block={ name } />
		</Disabled>
	</div>
);

export default ServerSideEdit;

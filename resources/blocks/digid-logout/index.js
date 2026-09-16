import { registerBlockType } from '@wordpress/blocks';
import ServerSideEdit from '../ServerSideEdit';
import metadata from './block.json';

registerBlockType( metadata.name, { edit: ServerSideEdit } );

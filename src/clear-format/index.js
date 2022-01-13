/**
 * External dependencies
 */
import { map } from 'lodash';

/**
 * WordPress dependencies
 */
import { select } from '@wordpress/data';
import { RichTextToolbarButton } from '@wordpress/block-editor';
import { registerFormatType, removeFormat } from '@wordpress/rich-text';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { editorRemoveformatting } from '../icons';

const formatName = 'rtex/rtex-clear-format';
const title = __( 'Clear format', 'richtext-extension' );

if ( rtexConf.clearFormatActive ) {
	registerFormatType( formatName, {
		title,
		tagName: 'span',
		className: 'rtex-clear-format',

		edit( { isActive, value, onChange } ) {
			const onToggle = () => {
				const formatTypes = select( 'core/rich-text' ).getFormatTypes();
				if ( 0 < formatTypes.length ) {
					let newValue = value;
					map( formatTypes, ( activeFormat ) => {
						newValue = removeFormat( newValue, activeFormat.name );
					} );
					onChange( { ...newValue } );
				}
			};

			return (
				<RichTextToolbarButton
					icon={ editorRemoveformatting }
					title={ title }
					onClick={ onToggle }
					isActive={ isActive }
				/>
			);
		},
	} );
}

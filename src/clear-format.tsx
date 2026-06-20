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
import { editorRemoveformatting } from './icons';
import type { FormatEditProps, FormatTypeSettings } from './types';

const formatName = 'rtex/rtex-clear-format';
const title = __( 'Clear format', 'richtext-extension' );

if ( rtexConf.clearFormatActive ) {
	registerFormatType( formatName, {
		title,
		tagName: 'span',
		className: 'rtex-clear-format',

		edit( { isActive, value, onChange }: FormatEditProps ) {
			const onToggle = () => {
				const formatTypes = select( 'core/rich-text' ).getFormatTypes();
				if ( 0 < formatTypes.length ) {
					let newValue = value;
					formatTypes.forEach( ( activeFormat: { name: string } ) => {
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
	} as unknown as FormatTypeSettings );
}

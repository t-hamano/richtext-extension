import { RichTextToolbarButton } from '@wordpress/block-editor';
import { registerFormatType, toggleFormat } from '@wordpress/rich-text';
import { __ } from '@wordpress/i18n';

const formatName = 'rtex/rtex-underline';
const title = __( 'Underline', 'richtext-extension' );

if ( rtexConf.underlineActive ) {
	registerFormatType( formatName, {
		title: title,
		tagName: 'u',
		className: null,

		edit( { isActive, value, onChange } ) {
			const onToggle = () => onChange( toggleFormat( value, {
				type: formatName
			} ) );

			return (
				<RichTextToolbarButton
					icon={ 'editor-underline' }
					title={ title }
					onClick={ onToggle }
					isActive={ isActive }
				/>
			);
		}
	});
}

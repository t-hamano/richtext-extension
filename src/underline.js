/**
 * WordPress dependencies
 */
import domReady from '@wordpress/dom-ready';
import { RichTextShortcut, RichTextToolbarButton } from '@wordpress/block-editor';
import { registerFormatType, unregisterFormatType, toggleFormat } from '@wordpress/rich-text';
import { formatUnderline } from '@wordpress/icons';
import { __ } from '@wordpress/i18n';

const formatName = 'rtex/rtex-underline';
const title = __( 'Underline', 'richtext-extension' );

const deprecatedFormatName = 'rtex/rtex-underline-deprecated';
const deprecatedTitle = __( 'Underline (Deprecated)', 'richtext-extension' );

domReady( () => {
	if ( rtexConf.underlineActive ) {
		unregisterFormatType( 'core/underline' );

		registerFormatType( deprecatedFormatName, {
			title: deprecatedTitle,
			tagName: 'u',
			className: null,
		} );

		registerFormatType( formatName, {
			title,
			tagName: 'span',
			className: null,
			attributes: {
				style: 'style',
			},
			edit( { isActive, value, onChange } ) {
				const onToggle = () =>
					onChange(
						toggleFormat( value, {
							type: formatName,
							attributes: {
								style: 'text-decoration: underline;',
							},
							title,
						} )
					);
				return (
					<>
						<RichTextShortcut type="primary" character="u" onUse={ onToggle } />
						<RichTextToolbarButton
							icon={ formatUnderline }
							title={ title }
							onClick={ onToggle }
							isActive={ isActive }
						/>
					</>
				);
			},
		} );
	}
} );

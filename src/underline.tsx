/**
 * WordPress dependencies
 */
import domReady from '@wordpress/dom-ready';
import { select } from '@wordpress/data';
import { RichTextToolbarButton } from '@wordpress/block-editor';
import { unregisterFormatType, toggleFormat } from '@wordpress/rich-text';
import { formatUnderline } from '@wordpress/icons';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { registerFormatType } from './register-format-type';
import type { FormatEditProps, FormatTypeSettings } from './types';

const formatName = 'core/underline';
const title = __( 'Underline', 'richtext-extension' );

const deprecatedFormatName = 'rtex/rtex-underline-deprecated';

domReady( () => {
	if ( ! rtexConf.underlineActive || ! select( 'core/rich-text' ).getFormatType( formatName ) ) {
		return;
	}

	// Re-register `core/underline` with a toolbar button, keeping the original
	// `edit` that handles the keyboard shortcut and the `formatUnderline` input event.
	const {
		name,
		edit: OriginalEdit,
		...settings
	} = unregisterFormatType( formatName ) as FormatTypeSettings & { name: string };

	registerFormatType( deprecatedFormatName, {
		title: __( 'Underline (Deprecated)', 'richtext-extension' ),
		tagName: 'u',
		className: null,
	} );

	registerFormatType( name, {
		...settings,
		edit( props: FormatEditProps ) {
			const { isActive, value, onChange } = props;
			return (
				<>
					{ OriginalEdit && <OriginalEdit { ...props } /> }
					<RichTextToolbarButton
						icon={ formatUnderline }
						title={ title }
						onClick={ () =>
							onChange(
								toggleFormat( value, {
									type: name,
									attributes: {
										style: 'text-decoration: underline;',
									},
									title,
								} as Parameters< typeof toggleFormat >[ 1 ] )
							)
						}
						isActive={ isActive }
					/>
				</>
			);
		},
	} );
} );

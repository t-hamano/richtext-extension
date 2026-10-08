/**
 * External dependencies
 */
import clsx from 'clsx';

/**
 * WordPress dependencies
 */
import { getActiveFormat, toggleFormat } from '@wordpress/rich-text';
import { ToolbarButton } from '@wordpress/components';
import { Menu } from '@wordpress/ui';
import { BlockFormatControls } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { registerFormatType } from './register-format-type';
import { adminAppearance as icon } from './icons';
import type { FormatEditProps } from './types';

const label = __( 'Highlighter', 'richtext-extension' );

rtexConf.highlighter.forEach( ( { title, className }, index ) => {
	registerFormatType( 'rtex/' + className, {
		title,
		tagName: 'span',
		className,
		...( index === 0 && {
			edit: ( { value, onChange }: FormatEditProps ) => {
				const hasActive = rtexConf.highlighter.some(
					( item ) => !! getActiveFormat( value, 'rtex/' + item.className )
				);
				return (
					<BlockFormatControls>
						<Menu.Root>
							<Menu.Trigger
								render={
									<ToolbarButton
										icon={ icon }
										label={ label }
										className={ clsx( { 'is-pressed': hasActive } ) }
									/>
								}
							/>
							<Menu.Popup>
								{ rtexConf.highlighter.map( ( item ) => {
									const formatName = 'rtex/' + item.className;
									return (
										<Menu.CheckboxItem
											key={ item.className }
											checked={ !! getActiveFormat( value, formatName ) }
											closeOnClick
											onCheckedChange={ () =>
												onChange( toggleFormat( value, { type: formatName } ) )
											}
										>
											<Menu.ItemLabel>
												<span className={ item.className }>{ item.title }</span>
											</Menu.ItemLabel>
										</Menu.CheckboxItem>
									);
								} ) }
							</Menu.Popup>
						</Menu.Root>
					</BlockFormatControls>
				);
			},
		} ),
	} );
} );

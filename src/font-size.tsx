/**
 * External dependencies
 */
import clsx from 'clsx';

/**
 * WordPress dependencies
 */
import { getActiveFormat, toggleFormat } from '@wordpress/rich-text';
import { ToolbarDropdownMenu, MenuGroup, MenuItem } from '@wordpress/components';
import { BlockFormatControls } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { textColor as icon } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import { registerFormatType } from './register-format-type';
import type { FormatEditProps } from './types';

const label = __( 'Font size', 'richtext-extension' );

rtexConf.fontSize.forEach( ( { title, className }, index ) => {
	registerFormatType( 'rtex/' + className, {
		title,
		tagName: 'span',
		className,
		...( index === 0 && {
			edit: ( { value, onChange }: FormatEditProps ) => {
				const hasActive = rtexConf.fontSize.some(
					( item ) => !! getActiveFormat( value, 'rtex/' + item.className )
				);
				return (
					<BlockFormatControls>
						<ToolbarDropdownMenu
							icon={ icon }
							label={ label }
							toggleProps={ {
								className: clsx( { 'is-pressed': hasActive } ),
							} }
							popoverProps={ {
								className: 'rtex-dropdown-popover',
							} }
						>
							{ ( { onClose } ) => (
								<MenuGroup>
									{ rtexConf.fontSize.map( ( item ) => {
										const formatName = 'rtex/' + item.className;
										const isSelected = !! getActiveFormat( value, formatName );
										return (
											<MenuItem
												key={ item.className }
												icon={ icon }
												iconPosition="left"
												className={ clsx( 'components-dropdown-menu__menu-item', {
													'is-active': isSelected,
												} ) }
												role="menuitemradio"
												isSelected={ isSelected }
												onClick={ () => {
													onClose();
													onChange( toggleFormat( value, { type: formatName } ) );
												} }
											>
												<span className={ item.className }>{ item.title }</span>
											</MenuItem>
										);
									} ) }
								</MenuGroup>
							) }
						</ToolbarDropdownMenu>
					</BlockFormatControls>
				);
			},
		} ),
	} );
} );

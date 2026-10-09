/**
 * External dependencies
 */
import clsx from 'clsx';

/**
 * WordPress dependencies
 */
import { getActiveFormat } from '@wordpress/rich-text';
import { ToolbarDropdownMenu, MenuGroup, MenuItem } from '@wordpress/components';
import { BlockFormatControls } from '@wordpress/block-editor';
import { __, sprintf } from '@wordpress/i18n';
import { check, textColor as icon } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import { registerFormatType } from './register-format-type';
import { toggleExclusiveFormat } from './toggle-exclusive-format';
import type { FormatEditProps } from './types';

const formatNames = rtexConf.fontSize.map( ( { className } ) => 'rtex/' + className );

rtexConf.fontSize.forEach( ( { title, className }, index ) => {
	registerFormatType( 'rtex/' + className, {
		title,
		tagName: 'span',
		className,
		...( index === 0 && {
			edit: ( { value, onChange }: FormatEditProps ) => {
				const hasActive = formatNames.some( ( name ) => !! getActiveFormat( value, name ) );
				return (
					<BlockFormatControls>
						<ToolbarDropdownMenu
							icon={ icon }
							label={ __( 'Font size', 'richtext-extension' ) }
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
												icon={ isSelected ? check : null }
												className="components-dropdown-menu__menu-item"
												role="menuitemradio"
												isSelected={ isSelected }
												onClick={ () => {
													onClose();
													onChange( toggleExclusiveFormat( value, formatName, formatNames ) );
												} }
											>
												{ sprintf(
													/* translators: 1: Font size title. 2: Font size with a unit, e.g. 1.3em. */
													__( '%1$s (%2$s)', 'richtext-extension' ),
													item.title,
													item.size
												) }
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

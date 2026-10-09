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
import { __ } from '@wordpress/i18n';
import { check } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import { registerFormatType } from './register-format-type';
import { toggleExclusiveFormat } from './toggle-exclusive-format';
import { adminAppearance as icon } from './icons';
import type { FormatEditProps } from './types';

const formatNames = rtexConf.highlighter.map( ( { className } ) => 'rtex/' + className );

rtexConf.highlighter.forEach( ( { title, className }, index ) => {
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
							label={ __( 'Highlighter', 'richtext-extension' ) }
							toggleProps={ {
								className: clsx( { 'is-pressed': hasActive } ),
							} }
							popoverProps={ {
								className: 'rtex-dropdown-popover',
							} }
						>
							{ ( { onClose } ) => (
								<MenuGroup>
									{ rtexConf.highlighter.map( ( item ) => {
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

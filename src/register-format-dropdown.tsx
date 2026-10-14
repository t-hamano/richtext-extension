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
import { check } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import { registerFormatType } from './register-format-type';
import { toggleExclusiveFormat } from './toggle-exclusive-format';
import type { FormatEditProps } from './types';

type DropdownItem = {
	title: string;
	className: string;
};

type DropdownOptions< T extends DropdownItem > = {
	label: string;
	icon: React.ComponentProps< typeof ToolbarDropdownMenu >[ 'icon' ];
	renderItemLabel: ( item: T ) => React.ReactNode;
};

/**
 * Registers a format type for each item and a toolbar dropdown to toggle them
 * exclusively.
 *
 * @param {Array}                     items                   Items to register as format types.
 * @param {Object}                    options                 Dropdown options.
 * @param {string}                    options.label           Dropdown label.
 * @param {Object}                    options.icon            Dropdown icon.
 * @param {(item: Object) => Element} options.renderItemLabel Renders the label of each menu item.
 */
export const registerFormatDropdown = < T extends DropdownItem >(
	items: T[],
	{ label, icon, renderItemLabel }: DropdownOptions< T >
): void => {
	const formatNames = items.map( ( { className } ) => 'rtex/' + className );

	items.forEach( ( { title, className }, index ) => {
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
										{ items.map( ( item ) => {
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
													{ renderItemLabel( item ) }
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
};

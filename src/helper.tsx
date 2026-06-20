/**
 * External dependencies
 */
import clsx from 'clsx';
import type { ComponentProps, ReactNode } from 'react';

/**
 * WordPress dependencies
 */
import { toggleFormat } from '@wordpress/rich-text';
import { ToolbarButton, ToolbarItem, DropdownMenu, createSlotFill } from '@wordpress/components';
import { BlockFormatControls } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import type { FormatEditProps, FormatTypeSettings, RichTextSettingArgs } from './types';

type Fill = { props: { isActive?: boolean; [ key: string ]: unknown } };

export const getRichTextSetting = (
	{ label, icon, title, className, slotFillName }: RichTextSettingArgs,
	index: number
): [ string, FormatTypeSettings ] => {
	const { Fill, Slot } = createSlotFill( slotFillName );
	const DropdownControls = Object.assign( Fill, { Slot } );

	const formatName = 'rtex/' + className;
	const component = ( { value, onChange, isActive }: FormatEditProps ) => {
		return (
			<DropdownControls>
				<ToolbarButton
					icon={ icon }
					// The bundled type only allows a string, but a styled label node is intended here.
					title={ ( <span className={ className }>{ title }</span> ) as unknown as string }
					onClick={ () => {
						onChange(
							toggleFormat( value, {
								type: formatName,
							} )
						);
					} }
					isActive={ isActive }
				/>
			</DropdownControls>
		);
	};

	const setting = {
		title,
		tagName: 'span',
		className,
		edit: ( args: FormatEditProps ) => {
			if ( ! index ) {
				return (
					<>
						{ component( args ) }
						<BlockFormatControls>
							<DropdownControls.Slot>
								{ ( fills: ReactNode ) => {
									const fillList = ( fills ?? [] ) as Fill[][];
									if ( ! fillList.length ) {
										return null;
									}

									const allProps = fillList.map( ( [ { props } ] ) => props );
									const hasActive = allProps.some( ( { isActive } ) => isActive );

									return (
										<ToolbarItem>
											{ ( toggleProps ) => (
												<DropdownMenu
													toggleProps={ {
														...toggleProps,
														className: clsx( toggleProps.className, {
															'is-pressed': hasActive,
														} ),
													} }
													popoverProps={ {
														className: 'rtex-dropdown-popover',
													} }
													icon={ icon }
													label={ label }
													controls={
														allProps as ComponentProps< typeof DropdownMenu >[ 'controls' ]
													}
												/>
											) }
										</ToolbarItem>
									);
								} }
							</DropdownControls.Slot>
						</BlockFormatControls>
					</>
				);
			}
			return component( args );
		},
	};
	return [ formatName, setting as unknown as FormatTypeSettings ];
};

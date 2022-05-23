/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { toggleFormat } from '@wordpress/rich-text';
import {
	ToolbarButton,
	ToolbarGroup,
	ToolbarItem,
	DropdownMenu,
	createSlotFill,
} from '@wordpress/components';
import { BlockFormatControls } from '@wordpress/block-editor';

export const getRichTextSetting = ( { label, icon, title, className, slotFillName }, index ) => {
	const { Fill, Slot } = createSlotFill( slotFillName );
	const DropdownControls = Fill;
	DropdownControls.Slot = Slot;

	const formatName = 'rtex/' + className;
	const component = ( { value, onChange, isActive } ) => {
		return (
			<DropdownControls>
				<ToolbarButton
					icon={ icon }
					title={ <div className={ className }>{ title }</div> }
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
		edit: ( args ) => {
			if ( ! index ) {
				return (
					<>
						{ component( args ) }
						<BlockFormatControls>
							<ToolbarGroup>
								<DropdownControls.Slot>
									{ ( fills ) => {
										if ( ! fills.length ) {
											return null;
										}

										const allProps = fills.map( ( [ { props } ] ) => props );
										const hasActive = allProps.some( ( { isActive } ) => isActive );

										return (
											<ToolbarItem>
												{ ( toggleProps ) => (
													<DropdownMenu
														toggleProps={ {
															...toggleProps,
															className: classnames( toggleProps.className, {
																'is-pressed': hasActive,
															} ),
														} }
														icon={ icon }
														label={ label }
														controls={ allProps }
													/>
												) }
											</ToolbarItem>
										);
									} }
								</DropdownControls.Slot>
							</ToolbarGroup>
						</BlockFormatControls>
					</>
				);
			}
			return component( args );
		},
	};
	return [ formatName, setting ];
};

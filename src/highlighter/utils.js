/**
 * WordPress dependencies
 */
import { toggleFormat } from '@wordpress/rich-text';
import { ToolbarButton } from '@wordpress/components';

/**
 * Internal dependencies
 */
import { adminAppearance } from '../icons';
import { Dropdown, DropdownControls } from './dropdown';

export const getRichTextSetting = ( { title, className, setting = {} }, index ) => {
	const formatName = 'rtex/' + className;
	const component = ( args ) => {
		return (
			<DropdownControls>
				<ToolbarButton
					icon={ adminAppearance }
					title={ <div className={ className }>{ title }</div> }
					onClick={ () => {
						args.onChange(
							toggleFormat( args.value, {
								type: formatName,
							} )
						);
					} }
					isActive={ args.isActive }
				/>
			</DropdownControls>
		);
	};

	setting.title = title;
	setting.tagName = 'span';
	setting.className = className;
	setting.edit = ( args ) => {
		if ( ! index ) {
			return (
				<>
					{ component( args ) }
					<Dropdown />
				</>
			);
		}
		return component( args );
	};
	return [ formatName, setting ];
};

import { Fragment } from '@wordpress/element';
import { toggleFormat } from '@wordpress/rich-text';
import { ToolbarButton } from '@wordpress/components';
import { textColor } from '@wordpress/icons';
import { Dropdown, DropdownControls } from './dropdown';

export const getRichTextSetting = ({ title, className, setting = {} }, index ) => {
	const formatName = 'rtex/' + className;
	const component = args => <DropdownControls>
		<ToolbarButton
			icon = { textColor }
			title = { <div className={ className }>{ title }</div> }
			onClick = { () => {
				args.onChange( toggleFormat( args.value, {
					type: formatName
				}) );
			} }
			isActive = { args.isActive }
		/>
	</DropdownControls>;

	setting.title = title;
	setting.tagName = 'span';
	setting.className = className;
	setting.edit = args => {
		if ( ! index ) {
			return <Fragment>
				{ component( args ) }
				<Dropdown/>
			</Fragment>;
		}
		return component( args );
	};
	return [ formatName, setting ];
};

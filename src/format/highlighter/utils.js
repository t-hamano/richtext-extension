const { Fragment } = wp.element;
const { toggleFormat } = wp.richText;
const { ToolbarButton } = wp.components;
import { Dropdown, DropdownControls } from './dropdown';
import { adminAppearance } from '../icons';

export const getRichTextSetting = ({ title, className, setting = {} }, index ) => {
	const formatName = 'rtex/' + className;
	const component = args => <DropdownControls>
		<ToolbarButton
			icon = { adminAppearance }
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

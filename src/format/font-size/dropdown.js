import { BlockFormatControls } from '@wordpress/block-editor';
import { Toolbar, DropdownMenu, createSlotFill } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const { Fill, Slot } = createSlotFill( 'FontSizeDropdownControls' );
const DropdownControls = Fill;
DropdownControls.Slot = Slot;

const Dropdown = () => <BlockFormatControls>
	<div className="editor-format-toolbar block-editor-format-toolbar">
		<div className="rtex-font-size-toolbar">
			<Toolbar>
				<DropdownControls.Slot>
					{ fills => <DropdownMenu
						icon='editor-textcolor'
						label={ __( 'Font size', 'richtext-extension' ) }
						controls={ fills.map( ([ { props } ]) => props ) }
					/> }
				</DropdownControls.Slot>
			</Toolbar>
		</div>
	</div>
</BlockFormatControls>;

export { Dropdown, DropdownControls };

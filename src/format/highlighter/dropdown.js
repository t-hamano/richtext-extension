import { BlockFormatControls } from '@wordpress/block-editor';
import { Toolbar, DropdownMenu, createSlotFill } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const { Fill, Slot } = createSlotFill( 'HighlighterDropdownControls' );
const DropdownControls = Fill;
DropdownControls.Slot = Slot;

const Dropdown = () => <BlockFormatControls>
	<div className="editor-format-toolbar block-editor-format-toolbar">
		<div className="rtex-highlighter-toolbar">
			<Toolbar>
				<DropdownControls.Slot>
					{ fills => <DropdownMenu
						icon='admin-appearance'
						label={ __( 'Highlighter', 'richtext-extension' ) }
						controls={ fills.map( ( [ { props } ] ) => props ) }
					/> }
				</DropdownControls.Slot>
			</Toolbar>
		</div>
	</div>
</BlockFormatControls>;

export { Dropdown, DropdownControls };

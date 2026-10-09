/**
 * WordPress dependencies
 */
import { Button, ColorIndicator, ColorPicker, Dropdown } from '@wordpress/components';

type ColorControlProps = {
	label: string;
	value: string;
	onChange: ( value: string ) => void;
};

export default function ColorControl( { label, value, onChange }: ColorControlProps ) {
	return (
		<Dropdown
			popoverProps={ { placement: 'bottom-start' } }
			renderToggle={ ( { isOpen, onToggle } ) => (
				<Button
					__next40pxDefaultSize
					className="rtex-settings-color-toggle"
					label={ label }
					aria-expanded={ isOpen }
					onClick={ onToggle }
				>
					<ColorIndicator colorValue={ value } />
				</Button>
			) }
			renderContent={ () => <ColorPicker color={ value } onChange={ onChange } /> }
		/>
	);
}

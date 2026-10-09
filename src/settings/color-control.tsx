/**
 * WordPress dependencies
 */
import { ColorIndicator, ColorPicker, Dropdown } from '@wordpress/components';
import { Button } from '@wordpress/ui';

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
					variant="unstyled"
					size="compact"
					className="rtex-settings-color-toggle"
					aria-label={ label }
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

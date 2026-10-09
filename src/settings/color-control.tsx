/**
 * WordPress dependencies
 */
import { ColorIndicator, ColorPicker, Dropdown } from '@wordpress/components';
import { useDebounce, useEvent } from '@wordpress/compose';
import { useState } from '@wordpress/element';
import { Button } from '@wordpress/ui';

type ColorControlProps = {
	label: string;
	value: string;
	onChange: ( value: string ) => void;
};

export default function ColorControl( { label, value, onChange }: ColorControlProps ) {
	// Keep the color locally while picking, as updating the entity record on every
	// pointer move makes the picker jittery. The change is propagated with a delay.
	const [ color, setColor ] = useState( value );
	const handleChange = useEvent( onChange );
	const debouncedOnChange = useDebounce( handleChange, 100 );

	return (
		<Dropdown
			popoverProps={ { placement: 'bottom-start' } }
			onToggle={ ( willOpen ) => {
				if ( willOpen ) {
					setColor( value );
				} else {
					debouncedOnChange.flush();
				}
			} }
			renderToggle={ ( { isOpen, onToggle } ) => (
				<Button
					variant="unstyled"
					size="compact"
					className="rtex-settings-color-toggle"
					aria-label={ label }
					aria-expanded={ isOpen }
					onClick={ onToggle }
				>
					<ColorIndicator colorValue={ isOpen ? color : value } />
				</Button>
			) }
			renderContent={ () => (
				<ColorPicker
					color={ color }
					onChange={ ( nextColor ) => {
						setColor( nextColor );
						debouncedOnChange( nextColor );
					} }
				/>
			) }
		/>
	);
}

/**
 * WordPress dependencies
 */
import { ColorIndicator, ColorPicker } from '@wordpress/components';
import { useDebounce, useEvent } from '@wordpress/compose';
import { useState } from '@wordpress/element';
import { Button, Popover } from '@wordpress/ui';

type ColorControlProps = {
	label: string;
	value: string;
	onChange: ( value: string ) => void;
};

export default function ColorControl( { label, value, onChange }: ColorControlProps ) {
	// Keep the color locally while picking, as updating the entity record on every
	// pointer move makes the picker jittery. The change is propagated with a delay.
	const [ color, setColor ] = useState( value );
	const [ isOpen, setIsOpen ] = useState( false );
	const handleChange = useEvent( onChange );
	const debouncedOnChange = useDebounce( handleChange, 100 );

	return (
		<Popover.Root
			open={ isOpen }
			onOpenChange={ ( willOpen ) => {
				if ( willOpen ) {
					setColor( value );
				} else {
					debouncedOnChange.flush();
				}
				setIsOpen( willOpen );
			} }
		>
			<Popover.Trigger
				render={
					<Button
						variant="unstyled"
						size="compact"
						className="rtex-settings-color-toggle"
						aria-label={ label }
					/>
				}
			>
				<ColorIndicator colorValue={ isOpen ? color : value } />
			</Popover.Trigger>
			<Popover.Popup positioner={ <Popover.Positioner side="bottom" align="start" /> }>
				<ColorPicker
					color={ color }
					onChange={ ( nextColor ) => {
						setColor( nextColor );
						debouncedOnChange( nextColor );
					} }
				/>
			</Popover.Popup>
		</Popover.Root>
	);
}

/**
 * WordPress dependencies
 */
import {
	RangeControl,
	// There is no stable equivalent of `UnitControl` in `@wordpress/components` or `@wordpress/ui`.
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUnitControl as UnitControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalParseQuantityAndUnitFromRawValue as parseQuantityAndUnitFromRawValue,
} from '@wordpress/components';
import { createInterpolateElement } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { InputControl, Stack, SwitchControl } from '@wordpress/ui';

/**
 * Internal dependencies
 */
import type { FontSizeSetting, FontSizeUnit } from './types';

/**
 * Settable font size range and step per unit.
 * Keep the ranges in sync with `Options::FONT_SIZE_RANGES`.
 */
const FONT_SIZE_UNITS: Record< FontSizeUnit, { min: number; max: number; step: number } > = {
	em: { min: 0.8, max: 3, step: 0.01 },
	rem: { min: 0.8, max: 3, step: 0.01 },
	px: { min: 10, max: 72, step: 1 },
};

const UNITS = ( Object.keys( FONT_SIZE_UNITS ) as FontSizeUnit[] ).map( ( unit ) => ( {
	value: unit,
	label: unit,
	step: FONT_SIZE_UNITS[ unit ].step,
} ) );

/**
 * Font size in pixels assumed for `em` and `rem` when converting to or from `px`.
 */
const BASE_FONT_SIZE = 16;

/**
 * Convert a font size to another unit, keeping it within the range of that unit.
 *
 * @param size Font size.
 * @param from Current unit.
 * @param to   New unit.
 * @return Converted font size.
 */
function convertFontSize( size: number, from: FontSizeUnit, to: FontSizeUnit ): number {
	let converted = size;

	if ( 'px' === from && 'px' !== to ) {
		converted = size / BASE_FONT_SIZE;
	} else if ( 'px' !== from && 'px' === to ) {
		converted = size * BASE_FONT_SIZE;
	}

	const { min, max, step } = FONT_SIZE_UNITS[ to ];
	const rounded = Number( ( Math.round( converted / step ) * step ).toFixed( 2 ) );

	return Math.min( Math.max( rounded, min ), max );
}

/**
 * Split a font size into its quantity and unit.
 *
 * @param size Font size with a unit, e.g. `1.3em`.
 * @return Quantity and unit.
 */
function parseFontSize( size: string ): [ number, FontSizeUnit ] {
	const [ quantity, unit ] = parseQuantityAndUnitFromRawValue( size, UNITS );

	return [ quantity ?? FONT_SIZE_UNITS.em.min, ( unit ?? 'em' ) as FontSizeUnit ];
}

type FontSizeTableProps = {
	items: FontSizeSetting[];
	onChange: ( index: number, changes: Partial< FontSizeSetting > ) => void;
};

export default function FontSizeTable( { items, onChange }: FontSizeTableProps ) {
	return (
		<div className="rtex-settings-table-wrap">
			<table className="rtex-settings-table">
				<thead>
					<tr>
						<th style={ { width: 1 } }>{ __( 'Status', 'richtext-extension' ) }</th>
						<th style={ { width: 200 } }>{ __( 'Title', 'richtext-extension' ) }</th>
						<th style={ { width: 250 } }>{ __( 'Size', 'richtext-extension' ) }</th>
						<th>{ __( 'Preview', 'richtext-extension' ) }</th>
					</tr>
				</thead>
				<tbody>
					{ items.map( ( item, index ) => {
						const [ quantity, unit ] = parseFontSize( item.size );

						return (
							<tr key={ index }>
								<td>
									<SwitchControl
										label={ sprintf(
											/* translators: %d: Font size number. */
											__( 'Enable font size %d', 'richtext-extension' ),
											index + 1
										) }
										hideLabelFromVision
										checked={ item.active }
										onCheckedChange={ ( checked ) => onChange( index, { active: checked } ) }
									/>
								</td>
								<td>
									<InputControl
										label={ sprintf(
											/* translators: %d: Font size number. */
											__( 'Title of font size %d', 'richtext-extension' ),
											index + 1
										) }
										hideLabelFromVision
										value={ item.title }
										onValueChange={ ( value ) => onChange( index, { title: value } ) }
									/>
								</td>
								<td>
									<Stack direction="row" gap="sm" align="center">
										<UnitControl
											__next40pxDefaultSize
											className="rtex-settings-unit-control"
											label={ sprintf(
												/* translators: %d: Font size number. */
												__( 'Size of font size %d', 'richtext-extension' ),
												index + 1
											) }
											hideLabelFromVision
											units={ UNITS }
											min={ FONT_SIZE_UNITS[ unit ].min }
											max={ FONT_SIZE_UNITS[ unit ].max }
											value={ item.size }
											onChange={ ( nextValue ) => {
												const [ nextQuantity, nextUnit ] = parseQuantityAndUnitFromRawValue(
													nextValue,
													UNITS
												);
												if ( nextQuantity === undefined || ! nextUnit ) {
													return;
												}
												// Changing the unit keeps the quantity, so convert the size instead.
												const size =
													nextUnit === unit
														? nextQuantity
														: convertFontSize( quantity, unit, nextUnit as FontSizeUnit );
												onChange( index, { size: `${ size }${ nextUnit }` } );
											} }
										/>
										<RangeControl
											className="rtex-settings-size-range"
											label={ sprintf(
												/* translators: %d: Font size number. */
												__( 'Size of font size %d', 'richtext-extension' ),
												index + 1
											) }
											hideLabelFromVision
											withInputField={ false }
											min={ FONT_SIZE_UNITS[ unit ].min }
											max={ FONT_SIZE_UNITS[ unit ].max }
											step={ FONT_SIZE_UNITS[ unit ].step }
											value={ quantity }
											onChange={ ( value ) => {
												if ( value !== undefined ) {
													onChange( index, { size: `${ value }${ unit }` } );
												}
											} }
										/>
									</Stack>
								</td>
								<td className="rtex-settings-font-size-preview">
									{ createInterpolateElement(
										__(
											'Hello World ! <span>Hello This World !</span> Hello World !',
											'richtext-extension'
										),
										{
											span: <span style={ { fontSize: item.size } } />,
										}
									) }
								</td>
							</tr>
						);
					} ) }
				</tbody>
			</table>
		</div>
	);
}

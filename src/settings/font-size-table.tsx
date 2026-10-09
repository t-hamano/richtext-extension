/**
 * WordPress dependencies
 */
import { RangeControl } from '@wordpress/components';
import { createInterpolateElement } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { InputControl, SelectControl, SwitchControl } from '@wordpress/ui';

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

const UNIT_ITEMS = ( Object.keys( FONT_SIZE_UNITS ) as FontSizeUnit[] ).map( ( unit ) => ( {
	value: unit,
	label: unit,
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
						<th style={ { width: 200 } }>{ __( 'Size', 'richtext-extension' ) }</th>
						<th style={ { width: 1 } }>{ __( 'Unit', 'richtext-extension' ) }</th>
						<th>{ __( 'Preview', 'richtext-extension' ) }</th>
					</tr>
				</thead>
				<tbody>
					{ items.map( ( item, index ) => (
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
								<RangeControl
									label={ sprintf(
										/* translators: %d: Font size number. */
										__( 'Size of font size %d', 'richtext-extension' ),
										index + 1
									) }
									hideLabelFromVision
									min={ FONT_SIZE_UNITS[ item.unit ].min }
									max={ FONT_SIZE_UNITS[ item.unit ].max }
									step={ FONT_SIZE_UNITS[ item.unit ].step }
									value={ item.size }
									onChange={ ( value ) => {
										if ( value !== undefined ) {
											onChange( index, { size: value } );
										}
									} }
								/>
							</td>
							<td>
								<SelectControl
									className="rtex-settings-unit-select"
									label={ sprintf(
										/* translators: %d: Font size number. */
										__( 'Unit of font size %d', 'richtext-extension' ),
										index + 1
									) }
									hideLabelFromVision
									items={ UNIT_ITEMS }
									value={ UNIT_ITEMS.find( ( unitItem ) => unitItem.value === item.unit ) }
									onValueChange={ ( selected ) => {
										if ( selected?.value ) {
											const unit = selected.value as FontSizeUnit;
											onChange( index, {
												size: convertFontSize( item.size, item.unit, unit ),
												unit,
											} );
										}
									} }
								/>
							</td>
							<td style={ { whiteSpace: 'nowrap' } }>
								{ createInterpolateElement(
									__(
										'Hello World ! <span>Hello This World !</span> Hello World !',
										'richtext-extension'
									),
									{
										span: <span style={ { fontSize: `${ item.size }${ item.unit }` } } />,
									}
								) }
							</td>
						</tr>
					) ) }
				</tbody>
			</table>
		</div>
	);
}

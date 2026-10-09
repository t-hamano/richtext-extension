/**
 * WordPress dependencies
 */
import { RangeControl } from '@wordpress/components';
import { createInterpolateElement } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { InputControl, SwitchControl } from '@wordpress/ui';

/**
 * Internal dependencies
 */
import type { FontSizeSetting } from './types';

/**
 * Settable font size range.
 * Keep in sync with `Options::MIN_FONT_SIZE` and `Options::MAX_FONT_SIZE`.
 */
const MIN_FONT_SIZE = 80;
const MAX_FONT_SIZE = 300;

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
						<th style={ { minWidth: 150 } }>{ __( 'Size', 'richtext-extension' ) }</th>
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
									__next40pxDefaultSize
									label={ sprintf(
										/* translators: %d: Font size number. */
										__( 'Size of font size %d', 'richtext-extension' ),
										index + 1
									) }
									hideLabelFromVision
									min={ MIN_FONT_SIZE }
									max={ MAX_FONT_SIZE }
									value={ item.size }
									onChange={ ( value ) => {
										if ( value !== undefined ) {
											onChange( index, { size: value } );
										}
									} }
								/>
							</td>
							<td>
								{ createInterpolateElement(
									__(
										'Hello World ! <span>Hello This World !</span> Hello World !',
										'richtext-extension'
									),
									{
										span: <span style={ { fontSize: `${ item.size / 100 }em` } } />,
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

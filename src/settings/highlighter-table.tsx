/**
 * WordPress dependencies
 */
import { RangeControl } from '@wordpress/components';
import { useMemo } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { InputControl, SelectControl, SwitchControl } from '@wordpress/ui';

/**
 * Internal dependencies
 */
import ColorControl from './color-control';
import { getHighlighterBackground } from './utils';
import type { HighlighterSetting, HighlighterType } from './types';

type HighlighterTableProps = {
	items: HighlighterSetting[];
	onChange: ( index: number, changes: Partial< HighlighterSetting > ) => void;
};

export default function HighlighterTable( { items, onChange }: HighlighterTableProps ) {
	const typeItems = useMemo(
		() => [
			{ value: 'solid', label: __( 'Solid', 'richtext-extension' ) },
			{ value: 'stripe', label: __( 'Stripe', 'richtext-extension' ) },
			{
				value: 'stripe-thin',
				label: __( 'Stripe (Thin)', 'richtext-extension' ),
			},
		],
		[]
	);

	return (
		<div className="rtex-settings-table-wrap">
			<table className="rtex-settings-table">
				<thead>
					<tr>
						<th>{ __( 'Status', 'richtext-extension' ) }</th>
						<th>{ __( 'Title', 'richtext-extension' ) }</th>
						<th>{ __( 'Color', 'richtext-extension' ) }</th>
						<th>{ __( 'Thickness', 'richtext-extension' ) }</th>
						<th>{ __( 'Opacity', 'richtext-extension' ) }</th>
						<th>{ __( 'Type', 'richtext-extension' ) }</th>
						<th>{ __( 'Preview', 'richtext-extension' ) }</th>
					</tr>
				</thead>
				<tbody>
					{ items.map( ( item, index ) => (
						<tr key={ index }>
							<td>
								<SwitchControl
									label={ sprintf(
										/* translators: %d: Highlighter number. */
										__( 'Enable highlighter %d', 'richtext-extension' ),
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
										/* translators: %d: Highlighter number. */
										__( 'Title of highlighter %d', 'richtext-extension' ),
										index + 1
									) }
									hideLabelFromVision
									value={ item.title }
									onValueChange={ ( value ) => onChange( index, { title: value } ) }
								/>
							</td>
							<td>
								<ColorControl
									label={ sprintf(
										/* translators: %d: Highlighter number. */
										__( 'Color of highlighter %d', 'richtext-extension' ),
										index + 1
									) }
									value={ item.color }
									onChange={ ( value ) => onChange( index, { color: value } ) }
								/>
							</td>
							<td>
								<RangeControl
									__next40pxDefaultSize
									label={ sprintf(
										/* translators: %d: Highlighter number. */
										__( 'Thickness of highlighter %d', 'richtext-extension' ),
										index + 1
									) }
									hideLabelFromVision
									min={ 0 }
									max={ 100 }
									value={ item.thickness }
									onChange={ ( value ) => {
										if ( value !== undefined ) {
											onChange( index, { thickness: value } );
										}
									} }
								/>
							</td>
							<td>
								<RangeControl
									__next40pxDefaultSize
									label={ sprintf(
										/* translators: %d: Highlighter number. */
										__( 'Opacity of highlighter %d', 'richtext-extension' ),
										index + 1
									) }
									hideLabelFromVision
									min={ 0 }
									max={ 100 }
									value={ item.opacity }
									onChange={ ( value ) => {
										if ( value !== undefined ) {
											onChange( index, { opacity: value } );
										}
									} }
								/>
							</td>
							<td>
								<SelectControl
									className="rtex-settings-type-select"
									label={ sprintf(
										/* translators: %d: Highlighter number. */
										__( 'Type of highlighter %d', 'richtext-extension' ),
										index + 1
									) }
									hideLabelFromVision
									items={ typeItems }
									value={ typeItems.find( ( typeItem ) => typeItem.value === item.type ) }
									onValueChange={ ( selected ) => {
										if ( selected?.value ) {
											onChange( index, {
												type: selected.value as HighlighterType,
											} );
										}
									} }
								/>
							</td>
							<td>
								<span
									style={ {
										background: getHighlighterBackground( item ),
									} }
								>
									{ __( 'Hello World !', 'richtext-extension' ) }
								</span>
							</td>
						</tr>
					) ) }
				</tbody>
			</table>
		</div>
	);
}

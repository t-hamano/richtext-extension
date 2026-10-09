/**
 * Internal dependencies
 */
import type { HighlighterSetting } from './types';

/**
 * Generate the CSS background value of a highlighter.
 * Keep in sync with `Enqueue::get_inline_css()`.
 *
 * @param highlighter Highlighter setting.
 * @return CSS background value.
 */
export function getHighlighterBackground( highlighter: HighlighterSetting ): string {
	const { color, opacity, thickness, type } = highlighter;
	let rgba = color;

	if ( opacity !== 100 ) {
		const r = parseInt( color.slice( 1, 3 ), 16 );
		const g = parseInt( color.slice( 3, 5 ), 16 );
		const b = parseInt( color.slice( 5, 7 ), 16 );
		rgba = `rgba(${ r }, ${ g }, ${ b }, ${ opacity / 100 })`;
	}

	if ( 'stripe' === type ) {
		return `repeating-linear-gradient(-45deg, ${ rgba } 0, ${ rgba } 3px, transparent 3px, transparent 6px) no-repeat bottom/100% ${ thickness }%`;
	}

	if ( 'stripe-thin' === type ) {
		return `repeating-linear-gradient(-45deg, ${ rgba } 0, ${ rgba } 2px, transparent 2px, transparent 4px) no-repeat bottom/100% ${ thickness }%`;
	}

	const position = 100 - thickness;

	return 0 === position
		? rgba
		: `linear-gradient(transparent ${ position }%, ${ rgba } ${ position }%)`;
}

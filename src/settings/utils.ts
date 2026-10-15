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
	const { opacity, thickness, type } = highlighter;
	// Append the opacity to the hex color as the alpha channel (#rrggbbaa).
	const alpha = Math.round( ( opacity * 255 ) / 100 );
	const color = highlighter.color + alpha.toString( 16 ).padStart( 2, '0' );

	if ( 'stripe' === type ) {
		return `repeating-linear-gradient(-45deg, ${ color } 0, ${ color } 3px, transparent 3px, transparent 6px) no-repeat bottom/100% ${ thickness }%`;
	}

	if ( 'stripe-thin' === type ) {
		return `repeating-linear-gradient(-45deg, ${ color } 0, ${ color } 2px, transparent 2px, transparent 4px) no-repeat bottom/100% ${ thickness }%`;
	}

	const position = 100 - thickness;

	return 0 === position
		? color
		: `linear-gradient(transparent ${ position }%, ${ color } ${ position }%)`;
}

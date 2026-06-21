/**
 * WordPress dependencies
 */
import { registerFormatType as registerFormatBase } from '@wordpress/rich-text';

/**
 * Internal dependencies
 */
import type { FormatTypeSettings } from './types';

/**
 * Registers a format type.
 *
 * The bundled type does not match the runtime API of `registerFormatType`, so
 * we define our own `FormatTypeSettings` type and keep the cast confined to
 * this single boundary.
 *
 * @param {string}             name     Format name.
 * @param {FormatTypeSettings} settings Format settings.
 */
export const registerFormatType = ( name: string, settings: FormatTypeSettings ): void => {
	registerFormatBase( name, settings as unknown as Parameters< typeof registerFormatBase >[ 1 ] );
};

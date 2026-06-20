/**
 * Internal dependencies
 */
import type { FormatConfig } from './types';

interface RtexConf {
	highlighter: FormatConfig[];
	fontSize: FormatConfig[];
	underlineActive: boolean;
	clearFormatActive: boolean;
}

declare global {
	const rtexConf: RtexConf;
}

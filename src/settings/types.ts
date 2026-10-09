export type HighlighterType = 'solid' | 'stripe' | 'stripe-thin';

export type HighlighterSetting = {
	active: boolean;
	title: string;
	color: string;
	thickness: number;
	opacity: number;
	type: HighlighterType;
};

export type FontSizeUnit = 'em' | 'rem' | 'px';

export type FontSizeSetting = {
	active: boolean;
	title: string;
	size: number;
	unit: FontSizeUnit;
};

export type Settings = {
	highlighter: HighlighterSetting[];
	font_size: FontSizeSetting[];
	underline_active: boolean;
	clear_format_active: boolean;
};

export type SiteSettings = {
	rtex_settings: Settings;
};

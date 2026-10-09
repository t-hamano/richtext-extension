/**
 * WordPress dependencies
 */
import { Button } from '@wordpress/components';
import { store as coreStore, useEntityRecord } from '@wordpress/core-data';
import { useDispatch, useSelect } from '@wordpress/data';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { SnackbarNotices, store as noticesStore } from '@wordpress/notices';
import { Card, CheckboxControl, Spinner, Stack } from '@wordpress/ui';

/**
 * Internal dependencies
 */
import FontSizeTable from './font-size-table';
import HighlighterTable from './highlighter-table';
import type { FontSizeSetting, HighlighterSetting, Settings, SiteSettings } from './types';

export default function App() {
	const { editedRecord, hasEdits, edit, save } = useEntityRecord< SiteSettings >(
		'root',
		'site',
		// The site entity is a singleton and has no record key.
		undefined as unknown as string
	);
	const [ isSaving, setIsSaving ] = useState( false );
	const { createSuccessNotice, createErrorNotice } = useDispatch( noticesStore );
	const { getEditedEntityRecord } = useSelect( coreStore );

	const settings = editedRecord?.rtex_settings;

	if ( ! settings ) {
		return <Spinner />;
	}

	// The whole object is sent on save, so always edit it as a whole. Read the latest
	// edited settings, as some controls propagate their changes with a delay.
	const updateSettings = ( getChanges: ( current: Settings ) => Partial< Settings > ) => {
		const { rtex_settings: current } = getEditedEntityRecord(
			'root',
			'site',
			undefined as unknown as string
		) as unknown as SiteSettings;
		edit( { rtex_settings: { ...current, ...getChanges( current ) } } );
	};

	const updateHighlighter = ( index: number, changes: Partial< HighlighterSetting > ) => {
		updateSettings( ( current ) => ( {
			highlighter: current.highlighter.map( ( item, i ) =>
				i === index ? { ...item, ...changes } : item
			),
		} ) );
	};

	const updateFontSize = ( index: number, changes: Partial< FontSizeSetting > ) => {
		updateSettings( ( current ) => ( {
			font_size: current.font_size.map( ( item, i ) =>
				i === index ? { ...item, ...changes } : item
			),
		} ) );
	};

	const onSave = async () => {
		setIsSaving( true );
		try {
			await save();
			createSuccessNotice( __( 'Settings saved.', 'richtext-extension' ), {
				type: 'snackbar',
			} );
		} catch {
			createErrorNotice( __( 'Failed to save settings.', 'richtext-extension' ), {
				type: 'snackbar',
			} );
		} finally {
			setIsSaving( false );
		}
	};

	return (
		<Stack className="rtex-settings" direction="column" gap="lg">
			<Card.Root>
				<Card.Header>
					<Card.Title render={ <h2 /> }>{ __( 'Highlighter', 'richtext-extension' ) }</Card.Title>
				</Card.Header>
				<Card.Content>
					<ul className="rtex-settings-notes">
						<li>
							{ __(
								'If the highlighter makes it hard to see the text, lower the opacity.',
								'richtext-extension'
							) }
						</li>
						<li>
							{ __(
								"If you change each setting, the style you're already applying to your content will also change.",
								'richtext-extension'
							) }
						</li>
					</ul>
					<HighlighterTable items={ settings.highlighter } onChange={ updateHighlighter } />
				</Card.Content>
			</Card.Root>
			<Card.Root>
				<Card.Header>
					<Card.Title render={ <h2 /> }>{ __( 'Font size', 'richtext-extension' ) }</Card.Title>
				</Card.Header>
				<Card.Content>
					<ul className="rtex-settings-notes">
						<li>
							{ __(
								'The size is specified as a percentage of the base font size.',
								'richtext-extension'
							) }
						</li>
						<li>
							{ __(
								"If you change each setting, the style you're already applying to your content will also change.",
								'richtext-extension'
							) }
						</li>
					</ul>
					<FontSizeTable items={ settings.font_size } onChange={ updateFontSize } />
				</Card.Content>
			</Card.Root>
			<Card.Root>
				<Card.Header>
					<Card.Title render={ <h2 /> }>{ __( 'Underline', 'richtext-extension' ) }</Card.Title>
				</Card.Header>
				<Card.Content>
					<CheckboxControl
						label={ __( 'Enable', 'richtext-extension' ) }
						checked={ settings.underline_active }
						onCheckedChange={ ( checked ) =>
							updateSettings( () => ( { underline_active: checked } ) )
						}
					/>
					<p>
						<strong>
							{ __(
								'Note: The underline specifications have changed from version 2.0.0. Try clearing the format if existing underlines do not work.',
								'richtext-extension'
							) }
						</strong>
					</p>
				</Card.Content>
			</Card.Root>
			<Card.Root>
				<Card.Header>
					<Card.Title render={ <h2 /> }>{ __( 'Clear format', 'richtext-extension' ) }</Card.Title>
				</Card.Header>
				<Card.Content>
					<CheckboxControl
						label={ __( 'Enable', 'richtext-extension' ) }
						checked={ settings.clear_format_active }
						onCheckedChange={ ( checked ) =>
							updateSettings( () => ( { clear_format_active: checked } ) )
						}
					/>
				</Card.Content>
			</Card.Root>
			<div>
				<Button
					__next40pxDefaultSize
					variant="primary"
					isBusy={ isSaving }
					disabled={ ! hasEdits || isSaving }
					accessibleWhenDisabled
					onClick={ onSave }
				>
					{ __( 'Save Changes', 'richtext-extension' ) }
				</Button>
			</div>
			<SnackbarNotices className="rtex-settings-snackbar" />
		</Stack>
	);
}

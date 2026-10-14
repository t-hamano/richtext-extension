/**
 * WordPress dependencies
 */
import { test, expect } from '@wordpress/e2e-test-utils-playwright';

test.describe( 'Settings', () => {
	let originalSettings: unknown;

	test.beforeAll( async ( { requestUtils } ) => {
		const siteSettings = await requestUtils.rest( { path: '/wp/v2/settings' } );
		originalSettings = siteSettings.rtex_settings;
	} );

	test.afterEach( async ( { requestUtils } ) => {
		await requestUtils.rest( {
			path: '/wp/v2/settings',
			method: 'POST',
			data: { rtex_settings: originalSettings },
		} );
	} );

	test.afterAll( async ( { requestUtils } ) => {
		await requestUtils.deleteAllPosts();
	} );

	test( 'should reflect saved settings in the editor and on the front end', async ( {
		admin,
		editor,
		page,
		requestUtils,
	} ) => {
		await admin.visitAdminPage( 'options-general.php', 'page=richtext-extension-option' );

		await page.getByRole( 'textbox', { name: 'Title of highlighter 1' } ).fill( 'Custom marker' );
		await page.getByRole( 'spinbutton', { name: 'Size of font size 1' } ).fill( '2' );
		await page.getByRole( 'button', { name: 'Save Changes' } ).click();
		await expect( page.getByTestId( 'snackbar' ) ).toContainText( 'Settings saved.' );

		// Editor.
		await admin.createNewPost();
		await editor.insertBlock( {
			name: 'core/paragraph',
			attributes: { content: 'Hello world' },
		} );
		await editor.canvas.getByRole( 'document', { name: 'Block: Paragraph' } ).click();

		await editor.clickBlockToolbarButton( 'Highlighter' );
		await expect( page.getByRole( 'menuitemradio', { name: 'Custom marker' } ) ).toBeVisible();
		await page.keyboard.press( 'Escape' );

		await editor.clickBlockToolbarButton( 'Font size' );
		await expect( page.getByRole( 'menuitemradio', { name: 'Extra small (2em)' } ) ).toBeVisible();

		// Front end.
		const post = await requestUtils.rest( {
			path: '/wp/v2/posts',
			method: 'POST',
			data: {
				title: 'Settings',
				content:
					'<!-- wp:paragraph --><p>Hello <span class="rtex-font-size-0">world</span></p><!-- /wp:paragraph -->',
				status: 'publish',
			},
		} );
		await page.goto( post.link );

		const span = page.locator( '.rtex-font-size-0' );
		const fontSizeRatio = await span.evaluate(
			( element ) =>
				parseFloat( window.getComputedStyle( element ).fontSize ) /
				parseFloat( window.getComputedStyle( element.parentElement! ).fontSize )
		);
		expect( fontSizeRatio ).toBeCloseTo( 2 );
	} );

	test( 'should hide disabled formats from the toolbar', async ( { admin, editor, page } ) => {
		await admin.visitAdminPage( 'options-general.php', 'page=richtext-extension-option' );

		await page.getByRole( 'switch', { name: 'Enable highlighter 1' } ).click();
		await page.getByRole( 'switch', { name: 'Enable font size 1' } ).click();
		await page.getByRole( 'switch', { name: 'Underline', exact: true } ).click();
		await page.getByRole( 'switch', { name: 'Clear format', exact: true } ).click();
		await page.getByRole( 'button', { name: 'Save Changes' } ).click();
		await expect( page.getByTestId( 'snackbar' ) ).toContainText( 'Settings saved.' );

		await admin.createNewPost();
		await editor.insertBlock( {
			name: 'core/paragraph',
			attributes: { content: 'Hello world' },
		} );
		await editor.canvas.getByRole( 'document', { name: 'Block: Paragraph' } ).click();

		await editor.clickBlockToolbarButton( 'Highlighter' );
		await expect( page.getByRole( 'menuitemradio', { name: 'Marker ( Yellow )' } ) ).toBeHidden();
		await expect( page.getByRole( 'menuitemradio', { name: 'Marker ( Red )' } ) ).toBeVisible();
		await page.keyboard.press( 'Escape' );

		await editor.clickBlockToolbarButton( 'Font size' );
		await expect( page.getByRole( 'menuitemradio', { name: 'Extra small (0.8em)' } ) ).toBeHidden();
		await expect( page.getByRole( 'menuitemradio', { name: 'Small (0.9em)' } ) ).toBeVisible();
		await page.keyboard.press( 'Escape' );

		await editor.clickBlockToolbarButton( 'More' );
		await expect( page.getByRole( 'menuitem', { name: 'Underline', exact: true } ) ).toBeHidden();
		await expect( page.getByRole( 'menuitem', { name: 'Clear format' } ) ).toBeHidden();
	} );
} );

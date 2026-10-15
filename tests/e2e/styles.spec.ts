/**
 * WordPress dependencies
 */
import { test, expect } from '@wordpress/e2e-test-utils-playwright';

const CONTENT =
	'<span class="rtex-highlighter-0">Hello</span> <span class="rtex-font-size-2">world</span>';

test.describe( 'Format styles', () => {
	test( 'should be applied in the post editor and on the front end', async ( {
		admin,
		editor,
		page,
	} ) => {
		await admin.createNewPost();
		await editor.insertBlock( {
			name: 'core/paragraph',
			attributes: { content: CONTENT },
		} );

		const editorHighlighter = editor.canvas.locator( '.rtex-highlighter-0' );
		const editorFontSize = editor.canvas.locator( '.rtex-font-size-2' );
		await expect( editorHighlighter ).toHaveCSS( 'background-image', /linear-gradient/ );
		await expect( editorFontSize ).toHaveCSS( 'font-size', /px$/ );
		const editorParagraphFontSize = await editor.canvas
			.getByRole( 'document', { name: 'Block: Paragraph' } )
			.evaluate( ( element ) => window.getComputedStyle( element ).fontSize );
		await expect( editorFontSize ).not.toHaveCSS( 'font-size', editorParagraphFontSize );

		const postId = await editor.publishPost();
		await page.goto( `/?p=${ postId }` );

		await expect( page.locator( '.rtex-highlighter-0' ) ).toHaveCSS(
			'background-image',
			/linear-gradient/
		);
		const frontParagraphFontSize = await page
			.locator( 'p', { has: page.locator( '.rtex-font-size-2' ) } )
			.evaluate( ( element ) => window.getComputedStyle( element ).fontSize );
		await expect( page.locator( '.rtex-font-size-2' ) ).not.toHaveCSS(
			'font-size',
			frontParagraphFontSize
		);
	} );

	test( 'should be applied in the site editor', async ( { admin, editor } ) => {
		await admin.visitSiteEditor( { canvas: 'edit' } );

		await expect( editor.canvas.locator( 'style#richtext-extension-inline-css' ) ).toHaveCount( 1 );
		await expect( editor.canvas.locator( 'style#richtext-extension-inline-css' ) ).toContainText(
			'.rtex-highlighter-0'
		);
	} );
} );

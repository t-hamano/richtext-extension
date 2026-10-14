/**
 * WordPress dependencies
 */
import { test, expect } from '@wordpress/e2e-test-utils-playwright';

test.describe( 'Formats', () => {
	test.beforeEach( async ( { admin, editor, pageUtils } ) => {
		await admin.createNewPost();
		await editor.insertBlock( {
			name: 'core/paragraph',
			attributes: { content: 'Hello world' },
		} );
		await editor.canvas.getByRole( 'document', { name: 'Block: Paragraph' } ).click();
		await pageUtils.pressKeys( 'primary+a' );
	} );

	test( 'should apply only one highlighter to the same text', async ( { editor, page } ) => {
		await editor.clickBlockToolbarButton( 'Highlighter' );
		await page.getByRole( 'menuitemradio', { name: 'Marker ( Yellow )' } ).click();

		await expect.poll( editor.getBlocks ).toMatchObject( [
			{
				name: 'core/paragraph',
				attributes: { content: '<span class="rtex-highlighter-0">Hello world</span>' },
			},
		] );

		await editor.clickBlockToolbarButton( 'Highlighter' );
		await page.getByRole( 'menuitemradio', { name: 'Marker ( Red )' } ).click();

		await expect.poll( editor.getBlocks ).toMatchObject( [
			{
				name: 'core/paragraph',
				attributes: { content: '<span class="rtex-highlighter-1">Hello world</span>' },
			},
		] );
	} );

	test( 'should apply only one font size to the same text', async ( { editor, page } ) => {
		await editor.clickBlockToolbarButton( 'Font size' );
		await page.getByRole( 'menuitemradio', { name: 'Extra small (0.8em)' } ).click();

		await expect.poll( editor.getBlocks ).toMatchObject( [
			{
				name: 'core/paragraph',
				attributes: { content: '<span class="rtex-font-size-0">Hello world</span>' },
			},
		] );

		await editor.clickBlockToolbarButton( 'Font size' );
		await page.getByRole( 'menuitemradio', { name: 'Large (1.3em)' } ).click();

		await expect.poll( editor.getBlocks ).toMatchObject( [
			{
				name: 'core/paragraph',
				attributes: { content: '<span class="rtex-font-size-2">Hello world</span>' },
			},
		] );
	} );

	test( 'should apply underline from the toolbar', async ( { editor, page } ) => {
		await editor.clickBlockToolbarButton( 'More' );
		await page.getByRole( 'menuitem', { name: 'Underline', exact: true } ).click();

		await expect.poll( editor.getBlocks ).toMatchObject( [
			{
				name: 'core/paragraph',
				attributes: {
					content: '<span style="text-decoration: underline;">Hello world</span>',
				},
			},
		] );
	} );

	test( 'should toggle underline with the keyboard shortcut', async ( { editor, pageUtils } ) => {
		await pageUtils.pressKeys( 'primary+u' );

		await expect.poll( editor.getBlocks ).toMatchObject( [
			{
				name: 'core/paragraph',
				attributes: {
					content: '<span style="text-decoration: underline;">Hello world</span>',
				},
			},
		] );

		await pageUtils.pressKeys( 'primary+u' );

		await expect
			.poll( editor.getBlocks )
			.toMatchObject( [ { name: 'core/paragraph', attributes: { content: 'Hello world' } } ] );
	} );

	test( 'should clear all formats', async ( { editor, page, pageUtils } ) => {
		await pageUtils.pressKeys( 'primary+u' );
		await editor.clickBlockToolbarButton( 'Highlighter' );
		await page.getByRole( 'menuitemradio', { name: 'Marker ( Yellow )' } ).click();
		await editor.clickBlockToolbarButton( 'Font size' );
		await page.getByRole( 'menuitemradio', { name: 'Large (1.3em)' } ).click();

		await expect
			.poll( async () => ( await editor.getBlocks() )[ 0 ].attributes.content )
			.toContain( 'rtex-font-size-2' );

		await editor.clickBlockToolbarButton( 'More' );
		await page.getByRole( 'menuitem', { name: 'Clear format' } ).click();

		await expect
			.poll( editor.getBlocks )
			.toMatchObject( [ { name: 'core/paragraph', attributes: { content: 'Hello world' } } ] );
	} );
} );

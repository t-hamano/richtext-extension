/**
 * WordPress dependencies
 */
import { getAdminThemeColors } from '@wordpress/admin-ui';
import domReady from '@wordpress/dom-ready';
import { createRoot } from '@wordpress/element';
import { ThemeProvider } from '@wordpress/theme';

/**
 * Internal dependencies
 */
import App from './app';
import './style.scss';

domReady( () => {
	const container = document.getElementById( 'rtex-settings' );

	if ( ! container ) {
		return;
	}

	const { primary } = getAdminThemeColors();

	createRoot( container ).render(
		<ThemeProvider isRoot color={ { primary } }>
			<App />
		</ThemeProvider>
	);
} );

import CountdownDigiD from './lib/countdown';

document.addEventListener( 'DOMContentLoaded', () => {
	const { sessionTTL, lastActivity, logoutLink } =
		window.owcGfDigidSession ?? {};

	if ( ! sessionTTL ) {
		return;
	}

	new CountdownDigiD( sessionTTL, lastActivity, logoutLink ).init();
} );

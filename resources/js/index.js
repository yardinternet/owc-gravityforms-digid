import Countdown from './lib/countdown';
import Logout from './lib/logout';

document.addEventListener('DOMContentLoaded', function() {
	const countdown = new Countdown();
	const logout = new Logout();

	countdown.init();
	logout.init();
});

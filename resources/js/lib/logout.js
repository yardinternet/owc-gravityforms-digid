export default class Logout {
	constructor() {
		this.button = document.getElementById('js-owc-gf-digid-logout');
	}

	/**
     * Initialize the plugin.
     */
	init() {
		this.registerEventHandlers();
	}

	/**
	 * Register event handlers.
	 */
	registerEventHandlers() {
		this.button.addEventListener('click', e => this.logout(e));
	}

	/**
	 * Handle logout.
	 */
	logout = () => {
		this.clearSessionTTL();
		this.clearSessionExpiration();

		return window.location.href = this.button.dataset.action;
	};

	/**
	 * Clear session TTL in Session Storage.
	 */
	clearSessionTTL = () => sessionStorage.removeItem('sessionTTL');

	/**
	 * Clear session expiration in Session Storage.
	 */
	clearSessionExpiration = () => sessionStorage.removeItem('sessionExpiration');
}

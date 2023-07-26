export default class Countdown {
	constructor() {
		this.sessionTTL = 0;
		this.sessionResumeTTL = 0;

		this.insertModalHTML();

		this.timerInterval;
		this.countDownInterval;
	}

	/**
	 * Get TTL value from Session Storage.
	 */
	get	getSessionTTL() {
		return JSON.parse(sessionStorage.getItem('sessionTTL'));
	}

	/**
	 * Get session expiration time from Session Storage.
	 */
	get getSessionExpiration() {
		return JSON.parse(sessionStorage.getItem('sessionExpiration'));
	}

	/**
	 * Set TTL value in Session Storage.
	 */
	set setSessionTTL(duration) {
		sessionStorage.setItem('sessionTTL', JSON.stringify(duration));
	}

	/**
	 * Set session expiration time in Session Storage.
	 */
	set setSessionExpiration(duration) {
		sessionStorage.setItem('sessionExpiration', JSON.stringify(duration));
	}

	/**
	 * Insert the session about to expire modal.
	 */
	insertModalHTML() {
		const gfWrapper = document.getElementsByClassName('gform_wrapper');

		if (gfWrapper) {
			gfWrapper[0].insertAdjacentHTML(
				'beforeend',
				`
			<div class='yda-digid-modal fade' id='modalWrapper' tabindex='-1' role='dialog' aria-labelledby='modalWrapper' aria-modal='true' aria-hidden='true'>
				<div id='modalDialog' class='yda-digid-modal-dialog' role='document'>
					<div class='yda-digid-modal-content'>
						<div class='yda-digid-modal-header'>
							<h5 class='yda-digid-modal-title' id='exampleModalLabel'>Uw sessie verloopt.</h5>
						</div>
						<div class='yda-digid-modal-body'>
							Uw sessie is mogelijk verlopen. Als u te lang niks hebt gedaan, wordt u uit veiligheidsoverwegingen door DigiD uitgelogd.
							Kies 'Verlengen' om uw sessie te verlengen, mogelijk moet u opnieuw inloggen met DigiD.
						</div>
						<div class='yda-digid-modal-footer'>
							<form action="/digid/logout" method="dialog">
								<button type='submit' id='js-abortSession' tabindex='0' role='button' class='yda-digid-modal-button' data-dismiss='modal'>Sluiten</button>
							</form>
							<button type='button' id='js-resumeSession' tabindex='0' role='button' class='yda-digid-modal-button'>
								Verlengen
							</button>
						</div>
					</div>
				</div>
			</div>`
			);
		}
	}

	/**
	 * Initialize the plugin.
	 */
	init() {
		this.registerEventHandlers();

		console.log('get', this.getSessionTTL)

		if (this.getSessionTTL) {
			const data = JSON.parse(this.getSessionExpiration);

			const now = new Date();
			const expiration = new Date(data);

			if (now.getTime() > expiration.getTime()) {
				return this.sessionEnd();
			} else {
				return this.sessionResumeCurrent();
			}
		}

		this.sessionStart();
	}

	/**
	 * Start the session.
	 * This is only a visual representation for the real session that goes on in the back-end.
	 */
	sessionStart() {
		console.log(this.setSessionTTL('test'));
		jQuery.ajax({
			url: gf_digid_ajax.ajax_url,
			type: 'POST',
			data: {
				action: 'digid_session_parameters'
			},
			success: (response) => {
				this.setSessionTTL(response.sessionTTL || 0);
				this.setSessionExpiration(Date.now() + this.sessionTTL * 1000);

				this.timerInit();
				this.initCountdown();
			},
			error: function(error) { console.log(error) }
		});

	}

	/**
	 * Resume the current session, calculate the time left.
	 * i.e. on page reload / tab switch.
	 */
	sessionResumeCurrent() {
		const sessionExpiration = JSON.parse(this.getSessionExpiration);

		const now = new Date().getTime();
		const expiration = new Date(sessionExpiration);
		const distance = expiration - now;
		const seconds = Math.floor((distance % (1000 * 60)) / 1000);

		this.setSessionTTL = seconds;

		this.timerInit();
		this.initCountdown();
	}

	/**
	 * Resume new session.
	 * i.e. on extending the TTL from the modal.
	 */
	sessionResumeNew() {
		const duration = Date.now() + this.sessionResumeTTL * 1000;

		this.closeModal();

		this.sessionTTL = JSON.stringify(this.sessionResumeTTL);
		this.sessionExpiration = JSON.stringify(duration);

		this.timerInit();
		this.initCountdown();
	}

	/**
	 * End session and go to logout page.
	 */
	sessionEnd = () => {
		this.clearSessionTTL();
		this.clearSessionExpiration();
	};

	/**
	 * Register event handlers.
	 */
	registerEventHandlers() {
		const resume = document.getElementById('js-resumeSession');
		const abort = document.getElementById('js-abortSession');
		const logout = document.getElementById('js-owc-gf-digid-logout');

		resume.addEventListener('click', (e) => this.sessionResumeNew(e));
		resume.addEventListener('keydown', (e) => this.a11yClick(e));
		abort.addEventListener('click', (e) => this.sessionEnd(e));
		abort.addEventListener('keydown', (e) => this.a11yClick(e));

		document.addEventListener('keydown', (e) => {
			const ESCAPE_KEY = 27;
			const modal = document.getElementById('modalWrapper');

			if (
				e.keyCode === ESCAPE_KEY &&
				modal.classList.contains('yda-digid-modal-show')
			) {
				this.sessionEnd();
				this.closeModal();
			}
		});

		if (logout) {
			logout.addEventListener('click', (e) => this.logout(e));
		}
	}

	/**
	 * Init countdown.
	 */
	initCountdown = () =>
		(this.countDownInterval = setInterval(this.countdown, 1000));

	/**
	 * Run countdown.
	 */
	countdown = () => {
		const countdownElem = document.getElementById(
			'js-owc-gf-digid-countdown'
		);
		console.log(this.getSessionTTL)
		if (!this.getSessionTTL) return;

		const expiration = JSON.parse(this.getSessionExpiration);
		const now = new Date().getTime();
		const distance = new Date(expiration) - now;

		// Time calculations for minutes and seconds.
		let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
		let seconds = Math.floor((distance % (1000 * 60)) / 1000);

		seconds = Math.round(seconds * 100) / 100;
		minutes = Math.round(minutes * 100) / 100;

		if (!!countdownElem) {
			countdownElem.textContent = `Resterende tijd: ${
				(minutes < 10 ? '0' : '') + minutes
			}:${(seconds < 10 ? '0' : '') + seconds}`;
		}
	};

	/**
	 * Stop countdown when timer is finished.
	 */
	stopCountdown = () => {
		const countdownElem = document.getElementById('js-owc-gf-digid-logout');
		clearInterval(this.countDownInterval);

		if (!!countdownElem) {
			countdownElem.textContent = 'Verlopen';
		}
	};

	/**
	 * Init timer interval.
	 */
	timerInit = () => (this.timerInterval = setInterval(this.timerStart, 1000));

	/**
	 * Is used for validating the session.
	 */
	timerStart = () => {
		const expiration = this.getSessionExpiration;

		if (Date.now() > expiration) {
			this.openModal();
		}

		return;
	};

	/**
	 * Stop timer; fires after the session is expired.
	 */
	stopTimer = () => clearInterval(this.timerInterval);

	/**
	 * Open modal.
	 */
	openModal = () => {
		this.stopTimer();
		this.stopCountdown();
		this.clearSessionTTL();

		const modalWrapper = document.getElementById('modalWrapper');

		// change state like in hidden modal.
		if (modalWrapper !== null) {
			modalWrapper.classList.add('yda-digid-modal-show');
			modalWrapper.setAttribute('aria-hidden', 'false');
		}

		this.startResumeCheck();
	};

	/**
	 * Close modal.
	 */
	closeModal = (e) => {
		const modalWrapper = document.getElementById('modalWrapper');

		if (modalWrapper !== null) {
			modalWrapper.classList.remove('yda-digid-modal-show');
			modalWrapper.setAttribute('aria-hidden', 'true');
		}
	};

	/**
	 * When modal is visible this function launches a timer.
	 */
	startResumeCheck() {
		const that = this;
		const resumeCheck = setInterval(function () {
			if (!that.sessionTTL) {
				that.sessionEnd();
				clearInterval(resumeCheck);
			} else {
				clearInterval(resumeCheck);
			}
		}, 10000);
	}

	/**
	 * Add keypress event to modal buttons.
	 *
	 * @param {object} e
	 */
	a11yClick = (e) => {
		const SPACE_KEY = 32;

		if (e.type !== 'click' || e.type !== 'keypress') return false;
		if (e.type === 'keypress') {
			const code = e.charCode || e.keyCode;
			if (code !== SPACE_KEY) return false;
		}

		return true;
	};

	/**
	 * Clear session TTL in Session Storage.
	 */
	clearSessionTTL = () => sessionStorage.removeItem('sessionTTL');

	/**
	 * Clear session expiration in Session Storage.
	 */
	clearSessionExpiration = () =>
		sessionStorage.removeItem('sessionExpiration');

	/**
	 * Handle logout.
	 */
	logout = () => {
		this.clearSessionTTL();
		this.clearSessionExpiration();
	};
}

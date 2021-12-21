import LogoutButton from "./logoutButton";
export default class Countdown {
	constructor(sessionTTL, resumeSessionTTL) {
		this.options = { sessionTTL, resumeSessionTTL }

		this.insertModalHTML();

		this.timerInterval;
		this.countDownInterval;
	}

	/**
	 * Get TTL value from Session Storage.
	 */
	get sessionTTL() {
		return sessionStorage.getItem('sessionTTL');
	}

	/**
	 * Get session expiration time from Session Storage.
	 */
	get sessionExpiration() {
		return sessionStorage.getItem('sessionExpiration');
	}

	/**
	 * Set TTL value in Session Storage.
	 */
	set sessionTTL(duration) {
		sessionStorage.setItem('sessionTTL', duration);
	}

	/**
	 * Set session expiration time in Session Storage.
	 */
	set sessionExpiration(duration) {
		sessionStorage.setItem('sessionExpiration', duration);
	}

	/**
	 * Set session expiration time in Session Storage.
	 */
	set moveLogoutButton() {
		const instance = new LogoutButton();
		instance.init();
	}

	/**
	 * Insert the session about to expire modal.
	 */
	insertModalHTML() {
		const gfWrapper = document.getElementsByClassName('gform_wrapper');

		if (gfWrapper) {
			gfWrapper[0].insertAdjacentHTML('beforeend', `
			<div class='modal fade' id='modalWrapper' tabindex='-1' role='dialog' aria-labelledby='modalWrapper' aria-modal='true' aria-hidden='true' style='display:none;'>
				<div id='modalDialog' class='modal-dialog' role='document'>
					<div class='modal-content'>
						<div class='modal-header'>
							<h5 class='modal-title' id='exampleModalLabel'>Uw sessie verloopt.</h5>
						</div>
						<div class='modal-body | mb-4'>
							Uw sessie is mogelijk verlopen. Als u te lang niks hebt gedaan, wordt u uit veiligheidsoverwegingen door DigiD uitgelogd.
							Kies 'Verlengen' om uw sessie te verlengen, mogelijk moet u opnieuw inloggen met DigiD.
						</div>
						<div class='modal-footer | d-flex justify-content-end'>
						<button type='button' id='js-abortSession' tabindex='0' role='button' class='btn btn-outline-primary mr-2' data-dismiss='modal'>Sluiten</button>
							<button type='button' id='js-resumeSession' tabindex='0' role='button' class='btn btn-primary'>Verlengen</button>
						</div>
					</div>
				</div>
			</div>`);
		}
	}

    /**
     * Initialize the plugin.
     */
	init() {
		const sessionTTL = this.sessionTTL;
		const sessionExpiration = this.sessionExpiration;

		this.registerEventHandlers();

		if (sessionTTL) {
			const data = JSON.parse(sessionExpiration);

			const now = new Date();
			const expiration = new Date(data);

			if (now.getTime() > expiration.getTime()) {
				return this.sessionEnd();
			} else {
				return this.sessionResumeCurrent();
			}
		}


		return this.sessionStart();
	}

	/**
	 * Start the session.
	 * This is only a visual representation for the real session that goes on in the back-end.
	 */
	sessionStart() {
		const duration = Date.now() + (this.options.sessionTTL * 1000);

		this.sessionTTL = JSON.stringify(this.options.sessionTTL);
		this.sessionExpiration = JSON.stringify(duration);

		this.timerInit();
		this.initCountdown();
	}

	/**
	 * Resume the current session, calculate the time left.
	 * i.e. on page reload / tab switch.
	 */
	sessionResumeCurrent() {
		const sessionExpiration = JSON.parse(this.sessionExpiration);

		const now = new Date().getTime();
		const expiration = new Date(sessionExpiration);
		const distance = expiration - now;
		const seconds = Math.floor((distance % (1000 * 60)) / 1000);

		this.sessionTTL = seconds;

		this.timerInit();
		this.initCountdown();
	}

	/**
	 * Resume new session.
	 * i.e. on extending the TTL from the modal.
	 */
	sessionResumeNew() {
		const duration = Date.now() + (this.options.resumeSessionTTL * 1000);

		this.closeModal();

		this.sessionTTL = JSON.stringify(this.options.resumeSessionTTL);
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

        const logoutLink = document.getElementById('logoutLink').href;
        return window.location.href = logoutLink;
    }

	/**
	 * Register event handlers.
	 */
	registerEventHandlers() {
		const resume = document.getElementById('js-resumeSession');
		const abort = document.getElementById('js-abortSession');
		const logout = document.getElementById('logoutLink');

		resume.addEventListener('click', e => this.sessionResumeNew(e));
		resume.addEventListener('keydown', e => this.a11yClick(e));
		abort.addEventListener('click', e => this.sessionEnd(e));
		abort.addEventListener('keydown', e => this.a11yClick(e));

		document.addEventListener('keydown', e => {
			const ESCAPE_KEY = 27;
			const modal = document.getElementById('modalWrapper');

			if (e.keyCode === ESCAPE_KEY && modal.classList.contains('show')) {
				this.sessionEnd();
				this.closeModal();
			}
		});

		if (logout) {
			logout.addEventListener('click', e => this.logout(e));
		}
	}

    /**
     * Init countdown.
     */
    initCountdown = () =>  this.countDownInterval = setInterval(this.countdown, 1000);

    /**
     * Run countdown.
     */
	countdown = () => {
		const countdownElem = document.getElementById('js-countdown');
		if (!this.sessionTTL) return;

		const expiration = JSON.parse(this.sessionExpiration);

        const now = new Date().getTime();
        const distance = new Date(expiration) - now;

        // Time calculations for minutes and seconds.
        let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        let seconds = Math.floor((distance % (1000 * 60)) / 1000);

		seconds = Math.round(seconds * 100) / 100;
        minutes = Math.round(minutes * 100) / 100;

        if (!!countdownElem) {
            countdownElem.textContent = `Resterende tijd: ${(minutes < 10 ? "0" : "") + minutes}:${(seconds < 10 ? "0" : "") + seconds}`;
        }
    }

    /**
     * Stop countdown when timer is finished.
     */
	stopCountdown = () => {
		const countdownElem = document.getElementById('js-countdown');
        clearInterval(this.countDownInterval);

        if (!!countdownElem) {
            countdownElem.textContent = 'Verlopen';
        }
    }

    /**
     * Init timer interval.
     */
    timerInit = () => this.timerInterval = setInterval(this.timerStart, 1000);

    /**
     * Is used for validating the session.
     */
	timerStart = () => {
		const expiration = JSON.parse(this.sessionExpiration);

        if (Date.now() > expiration) {
            this.openModal();
        }

        return;
    }

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
        const modalDialog = document.getElementById('modalDialog');

        // change state like in hidden modal.
        if (modalWrapper !== null) {
            modalWrapper.classList.add('show');
            modalWrapper.setAttribute('aria-hidden', 'false');
            modalWrapper.style.cssText = 'display: block; position: fixed; top: 0; bottom: 0; left: 0; right: 0; background-color: #666; opacity: 1; z-index: 1272;';
        }

        if (modalDialog !== null) {
            modalDialog.style.cssText = 'max-width: 500px; margin: 5rem auto; background-color: #ffffff; padding: 2rem;';
        }

        this.startResumeCheck();
	}

	/**
	 * Close modal.
	 */
	closeModal = e => {
		const modal = document.getElementById('modalWrapper');
		const modalDialog = document.getElementById('modalDialog');

		if (modal !== null) {
			modal.classList.remove('show');
			modal.setAttribute('aria-hidden', 'true');
			modal.style.cssText = 'display: none;';
		}

		if (modalDialog !== null) {
			modalDialog.style.cssText = "";
		}
	}

    /**
     * When modal is visible this function launches a timer.
     */
    startResumeCheck() {
		const that = this;
        const resumeCheck = setInterval(function() {
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
	a11yClick = e => {
		const SPACE_KEY = 32;

		if (e.type !== 'click' || e.type !== 'keypress') return false;

        if (e.type === 'keypress'){
            const code = e.charCode || e.keyCode;
            if (code !== SPACE_KEY) return false;
		}

		return true;
	}

	/**
	 * Clear session TTL in Session Storage.
	 */
	clearSessionTTL = () => sessionStorage.removeItem('sessionTTL');

	/**
	 * Clear session expiration in Session Storage.
	 */
	clearSessionExpiration = () => sessionStorage.removeItem('sessionExpiration');

	/**
	 * Handle logout.
	 */
	logout = () => {
		this.clearSessionTTL();
		this.clearSessionExpiration();
	};
}

export class Countdown {
	constructor(sessionTTL, resumeSessionTTL) {
		this.options = {
			sessionTTL,
			resumeSessionTTL
		}

		this.insertModalHTML();

		this.timerInterval;
		this.countDownInterval;
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
						<button type='button' id='js-abortSession' tabindex='0' role='button' class='btn btn-outline-primary' data-dismiss='modal'>Sluiten</button>
							<button type='button' id='js-resumeSession' tabindex='0' role='button' class='btn btn-primary mr-2'>Verlengen</button>
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
		if (!localStorage.sessionTTL) {
			this.sessionStart();
		} else {
			this.sessionResume();
		}

		this.registerEventHandlers();
	}

	/**
	 * Start the local storage session.
	 * This is only a visual representation for the real session that goes on in the back-end.
	 */
	sessionStart() {
		const format = this.createTTL(this.options.sessionTTL);
		localStorage.sessionTTL = JSON.stringify(format);

		this.initTimer();
		this.initCountdown();
	}

	/**
	 * Resume the current session, calculate the time left.
	 */
	sessionResume() {
		const parse = JSON.parse(localStorage.sessionTTL);
        const now = new Date().getTime();
        const distance = new Date(parse.expiry) - now;
		const seconds = Math.floor((distance % (1000 * 60)) / 1000);

		this.options.sessionTTL = seconds;

		this.initTimer();
		this.initCountdown();
	}

	/**
     * End session and go to logout page.
     */
    sessionEnd = () => {
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

		resume.addEventListener('click', e => this.sessionResume(e));
		resume.addEventListener('keydown', e => this.a11yClick(e));
		abort.addEventListener('click', e => this.sessionEnd(e));
		abort.addEventListener('keydown', e => this.a11yClick(e));

		document.addEventListener('keydown', e => this.closeModal(e));

		if (logout) {
			logout.addEventListener('click', e => this.logout(e));
		}
	}

    /**
     * Format TTL object for local storage.
     *
     * @param {int} seconds
     */
    createTTL = seconds => {
        return {
            value: seconds,
            expiry: Date.now() + (seconds * 1000)
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
		if (!localStorage.sessionTTL) return;

        const now = new Date().getTime();
        const distance = new Date(this.parseJSON()) - now;

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
     * Create object and return property: expiry.
     * Property is used for validating the session.
     */
	parseJSON = () => JSON.parse(localStorage.sessionTTL).expiry;

    /**
     * Init timer interval.
     */
    initTimer = () => this.timerInterval = setInterval(this.beginTimer, 1000);

    /**
     * Is used for validating the session.
     */
    beginTimer = () => {
        if (undefined === localStorage.sessionTTL && ! this._logoutClicked) {
            this.openModal();
        }

        if (Date.now() > this.parseJSON() && localStorage.sessionTTL) {
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
        localStorage.removeItem('sessionTTL');

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
		const ESCAPE_KEY = 27;
		const modal = document.getElementById('modalWrapper');

		if (e.keyCode === ESCAPE_KEY && modal.classList.contains('show')) {
			this.sessionEnd();

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
	}

    /**
     * When modal is visible this function launches a timer.
     */
    startResumeCheck() {
		const that = this;
        const resumeCheck = setInterval(function() {
            if (undefined === localStorage.sessionTTL) {
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
	 * Handle logout.
	 */
	logout = () => localStorage.removeItem('sessionTTL');
}

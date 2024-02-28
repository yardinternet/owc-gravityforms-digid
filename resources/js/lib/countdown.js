export default class CountdownDigiD {
	constructor( sessionTTL, lastActivity, logoutLink ) {
		this.second = 1000;
		this.minute = 60 * this.second;

		const tenSeconds = 10 * this.second;

		sessionTTL = sessionTTL * this.second - tenSeconds; // js session should end 10 seconds before php session expires

		this.modalTTL = this.minute;
		this.modalShouldOpen = sessionTTL - this.modalTTL;
		this.lastActivity = lastActivity * this.second;

		this.modalTimeout = undefined;
		this.timerInterval = undefined;
		this.logoutLink = logoutLink;
	}

	init() {
		this.registerEventHandlers();
		this.sessionHeartbeat();
		this.timerInit();
	}

	registerEventHandlers() {
		const resume = document.getElementById( 'js-resumeSession-DigiD' );
		const abort = document.getElementById( 'js-abortSession-DigiD' );

		if( !resume || !abort ) return;

		resume.addEventListener( 'click', ( e ) => this.sessionResume( e ) );
		resume.addEventListener( 'keydown', ( e ) => this.a11yClick( e ) );
		abort.addEventListener( 'click', ( e ) => this.logout( e ) );
		abort.addEventListener( 'keydown', ( e ) => this.a11yClick( e ) );

		document.addEventListener( 'keydown', ( e ) => {
			const ESCAPE_KEY = 27;
			const modal = document.getElementById( 'modalWrapperDigiD' );
			if (
				e.keyCode === ESCAPE_KEY &&
				modal.classList.contains( 'show' )
			) {
				this.logout();
			}
		} );

		document.addEventListener( 'mousemove', ( e ) =>
			this.updateLastActivity( e )
		);
		document.addEventListener( 'keydown', ( e ) =>
			this.updateLastActivity( e )
		);
	}

	timerInit = () =>
		( this.timerInterval = setInterval(
			this.checkSessionStatus,
			this.second
		) );
	sessionHeartbeat = () =>
		setInterval( this.maybeKeepSessionAlive, this.minute );

	modalTimer() {
		this.modalTimeout = setTimeout( () => {
			this.logout();
		}, this.modalTTL );
	}

	stopModalTimer() {
		if ( this.modalTimeout ) {
			clearTimeout( this.modalTimeout );
		}
	}

	checkSessionStatus = () => {
		if ( Date.now() - this.lastActivity > this.modalShouldOpen ) {
			clearInterval( this.timerInterval );
			this.openModal();
		}
	};

	maybeKeepSessionAlive = () => {
		if ( this.lastActivityIsUpdated ) {
			this.keepSessionAlive();
		}

		this.lastActivityIsUpdated = false;
	};

	sessionResume() {
		this.closeModal();
		this.updateLastActivity();
		this.keepSessionAlive();
		this.timerInit();
	}

	openModal = () => {
		this.modalTimer();

		const modalWrapperDigiD =
			document.getElementById( 'modalWrapperDigiD' );
		const modalDialogDigiD = document.getElementById( 'modalDialogDigiD' );

		// change state like in hidden modal.
		if ( modalWrapperDigiD !== null ) {
			modalWrapperDigiD.classList.add( 'show' );
			modalWrapperDigiD.setAttribute( 'aria-hidden', 'false' );
			modalWrapperDigiD.style.cssText =
				'display: block; position: fixed; top: 0; bottom: 0; left: 0; right: 0; background-color: #666; opacity: 1; z-index: 1272;';
		}

		if ( modalDialogDigiD !== null ) {
			modalDialogDigiD.style.cssText =
				'max-width: 500px; margin: 5rem auto; background-color: #ffffff; padding: 2rem;';
		}
	};

	closeModal = () => {
		this.stopModalTimer();

		const modal = document.getElementById( 'modalWrapperDigiD' );
		const modalDialogDigiD = document.getElementById( 'modalDialogDigiD' );

		if ( modal !== null ) {
			modal.classList.remove( 'show' );
			modal.setAttribute( 'aria-hidden', 'true' );
			modal.style.cssText = 'display: none;';
		}

		if ( modalDialogDigiD !== null ) {
			modalDialogDigiD.style.cssText = '';
		}
	};

	updateLastActivity = () => {
		this.lastActivity = Date.now();
		this.lastActivityIsUpdated = true;
	};

	logout = () => {
		window.location = this.logoutLink;
	};

	keepSessionAlive = () => {
		fetch( '/digid/keep_alive' );
	};

	/**
	 * Add keypress event to modal buttons.
	 *
	 * @param {object} e
	 */
	a11yClick = ( e ) => {
		const SPACE_KEY = 32;

		if ( e.type !== 'click' || e.type !== 'keypress' ) return false;
		if ( e.type === 'keypress' ) {
			const code = e.charCode || e.keyCode;
			if ( code !== SPACE_KEY ) return false;
		}

		return true;
	};
}

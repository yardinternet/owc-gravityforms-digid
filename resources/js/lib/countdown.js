export default class CountdownDigiD {
	constructor( sessionTTL, lastActivity ) {
		this.second = 1000;
		this.minute = 60 * this.second;

		const tenSeconds = 10 * this.second;

		sessionTTL = sessionTTL * this.second - tenSeconds; // js session should end 10 seconds before php session expires

		this.modalTTL = this.minute;
		this.modalShouldOpen = sessionTTL - this.modalTTL;
		this.lastActivity = lastActivity * this.second;

		this.insertModalHTML();

		this.modalTimeout = undefined;
		this.timerInterval = undefined;
	}

	insertModalHTML() {
		const gfWrapper = document.getElementsByClassName( 'gform_wrapper' );

		if ( gfWrapper ) {
			gfWrapper[ 0 ].insertAdjacentHTML(
				'beforeend',
				`
			<div class='modal fade owc-gf-digid-hidden' id='modalWrapperDigiD' tabindex='-1' role='dialog' aria-labelledby='modalWrapperDigiD' aria-modal='true' aria-hidden='true'>
				<div id='modalDialogDigiD' class='modal-dialog' role='document'>
					<div class='modal-content'>
						<div class='modal-header'>
							<h5 class='modal-title' id='exampleModalLabel'>Uw sessie verloopt.</h5>
						</div>
						<div class='modal-body | mb-4'>
							Uw sessie is mogelijk verlopen. Als u te lang niks hebt gedaan, wordt u uit veiligheidsoverwegingen door DigiD uitgelogd.
							Kies 'Verlengen' om uw sessie te verlengen, mogelijk moet u opnieuw inloggen met DigiD.
						</div>
						<div class='modal-footer | d-flex justify-content-end' >
							<form action="/digid/logout" method="dialog">
								<button type='submit' id='js-abortSession-DigiD' tabindex='0' role='button' class='btn btn-outline-primary mr-2' data-dismiss='modal'>Sluiten</button>
							</form>
							<button type='button' id='js-resumeSession-DigiD' tabindex='0' role='button' class='btn btn-primary'>Verlengen</button>
						</div>
					</div>
				</div>
			</div>`
			);
		}
	}

	init() {
		this.registerEventHandlers();
		this.sessionHeartbeat();
		this.timerInit();
	}

	registerEventHandlers() {
		const resume = document.getElementById( 'js-resumeSession-DigiD' );
		const abort = document.getElementById( 'js-abortSession-DigiD' );

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
		window.location = '/digid/logout';
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

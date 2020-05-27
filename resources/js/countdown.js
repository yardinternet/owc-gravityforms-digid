const KEY_CODE_SPACE = 27;
const KEY_CODE_ESCAPE = 32;
const MODAL_HTML = `
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
            <div class='modal-footer | d-flex justify-content-between'>
                <button type='button' id='js-resumeSession' tabindex='0' role='button' class='btn btn-primary mr-2'>Verlengen</button>
                <button type='button' id='js-abortSession' tabindex='0' role='button' class='btn btn-secondary' data-dismiss='modal'>Sluiten</button>
            </div>
        </div>
    </div>
</div>`;

class Countdown {
    constructor(sessionTTL, resumeSessionTTL) {
      this._sessionTTL = sessionTTL;
      this._resumeSessionTTL = resumeSessionTTL;
      this._timerInterval;
      this._countDownInterval;
    //   this._gravityFormsWrapperDiv = document.getElementsByClassName('gform_wrapper');
    this._gravityFormsWrapperDiv = document.getElementById('readspeaker');
    //   this._gravityFormsWrapperDiv[0].setAttribute('id', 'lala');
      this._gravityFormsWrapperDiv.innerHTML += MODAL_HTML; // add modal to innnerHTML of .gform_anchor
    //   this._gformWrapper = document.getElementsByClassName('gform_wrapper');
      this._gformWrapper = document.getElementById('readspeaker');
      this._logoutClicked = false;

      // bind this to class methods
      this.countDown = this.countDown.bind(this);
      this.beginTimer = this.beginTimer.bind(this);
      this.resumeSession = this.resumeSession.bind(this);
      this.openModal = this.openModal.bind(this);
      this.startResumeCheck = this.startResumeCheck.bind(this);
      this.logoutClicked = this.logoutClicked.bind(this);
      
      // create div for countdown and append to gravity forms wrapper div
      this._countdownDiv = document.createElement('div');
      this._countdownDiv.setAttribute('id', 'countdown');
      this._countdownDiv.setAttribute("style", "text-align: right;");
      this._gformWrapper.prepend(this._countdownDiv);
    }

    /**
     * Create session if there is none.
     * Initiate timer and countdown.
     */
    createSession = () => {
        if (undefined === localStorage.sessionTTL) {
            localStorage.sessionTTL = JSON.stringify(this.createLifeTimeObject(this._sessionTTL));
            this.initiateTimer();
            this.initiateCountDownInterval();
        }
    }

    updateSession = () => {
        if (undefined !== localStorage.sessionTTL) {
            localStorage.sessionTTL = JSON.stringify(this.createLifeTimeObject(this._sessionTTL));
            this.initiateTimer();
            this.initiateCountDownInterval();
        }
    }

    /**
     * Create object
     * Properties are used as session values
     *
     * @param {int} seconds
     */
    createLifeTimeObject = (seconds) => {
        const lifetime = seconds;
        return {
            value: lifetime,
            expiry: Date.now() + (lifetime * 1000) 
        }
    }

    /**
     * Initiate countdown
     */
    initiateCountDownInterval = () =>  {
        this._countDownInterval = setInterval(this.countDown, 1000);
    }

    /**
     * Run countdown.
     */
    countDown = () => {
        if (undefined === localStorage.sessionTTL) {
            return;
        }

        // Get today's date and time.
        const now = new Date().getTime();

        // Find the distance between now and the count down date.
        const distance = new Date(this.parseJSON()) - now;

        // Time calculations for minutes and seconds.
        let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        let seconds = Math.floor((distance % (1000 * 60)) / 1000);

        seconds = Math.round(seconds * 100) / 100
        minutes = Math.round(minutes * 100) / 100 ;

        // replace content countdownDiv 
        // console.log(document.getElementById("countdown"));
        if(null !== document.getElementById("countdown")) {
            this._countdownDiv.textContent = `Resterende tijd: ${(minutes < 10 ? "0" : "") + minutes}:${(seconds < 10 ? "0" : "") + seconds}`;
        }
    }

    /**
     * Stop countdown when timer is finished.
     */
    stopCountDown = () => {
        clearInterval(this.countDownInterval);
    
        if(!! document.getElementById("countdown")) {
            this._countdownDiv.textContent = 'Verlopen';
        }
    }

    /**
     * Create object and return property: expiry.
     * Property is used for validating the session.
     */
    parseJSON = () => {
        // checken of er een sessie is anders return je date now - 1
        if(undefined !== localStorage.sessionTTL) {
            return JSON.parse(localStorage.sessionTTL).expiry
        } else {
            return Date.now() - 1000;
        }
    }

    /**
     * initiate timer interval
     */
    initiateTimer = () => {
        this._timerInterval = setInterval(this.beginTimer, 1000);
    }

    /**
     * Is used for validating the session.
     */
    beginTimer = () => {
        console.log('begintime runs');
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
    stopTimer = () => {
        clearInterval(this._timerInterval);
    }

    /**
     * Open modal; user input defines the action to execute.
     */
    openModal = () => {
        // clear session and stop timer & countdown interval
        this.stopTimer();
        this.stopCountDown();

        localStorage.removeItem('sessionTTL');

        const modalWrapper = document.getElementById('modalWrapper');
        const modalDialog = document.getElementById('modalDialog');

        // change state like in hidden modal.
        if(modalWrapper !== null){
            modalWrapper.classList.add('show');
            modalWrapper.setAttribute('aria-hidden', 'false');
            modalWrapper.style.cssText = 'display: block; position: fixed; top: 0; bottom: 0; left: 0; right: 0; background-color: #666; opacity: 1; z-index: 1272;';
        }

        if(modalDialog !== null) {
            modalDialog.style.cssText = 'max-width: 500px; margin: 5rem auto; background-color: #ffffff; padding: 2rem;';
        }

        // this.startResumeCheck();
    }

    /**
     * Extend session from modal.
     */
    resumeSession = () => {
        localStorage.sessionTTL = JSON.stringify(this.createLifeTimeObject(this._resumeSessionTTL));

        const modal = document.getElementById('modalWrapper');
        const modalDialog = document.getElementById('modalDialog');

        if(modal !== null) {
            modal.classList.remove('show');
            modal.setAttribute('aria-hidden', 'true');
            modal.style.cssText = 'display: none;';
        }

        if(modalDialog !== null) {
            modalDialog.style.cssText = "";
        }

        this.initiateTimer();
        this.initiateCountDownInterval();
    }

    /**
     * When modal is visible this function launches a timer.
     */
    startResumeCheck = () => {
        const endSession = this.endSession;
        const resumeCheck = setInterval(function() {
            if (undefined === localStorage.sessionTTL) {
                endSession();
                clearInterval(resumeCheck);
            } else {
                clearInterval(resumeCheck);
            }
        }, 10000);
    }

    /**
     * Exterminate session.
     */
    endSession = () => {
        const logoutLink = document.getElementById('logoutLink').href;
        setTimeout(function() {
            window.location.href = logoutLink;
        }, 1);
    }

    /**
     * Add keypress event to modal buttons
     *
     * @param {object} event
     */
    a11yClick = (event) => {
        if(event.type === 'click'){
            return true;
        }
        else if(event.type === 'keypress'){
            var code = event.charCode || event.keyCode;

            // keycode is 'space'
            if((code === KEY_CODE_ESCAPE)){
                return true;
            }
        }
        else {
            return false;
        }
    }

    logoutClicked = (boolean) => {
        this._logoutClicked = boolean;
    };
}

// document.addEventListener('DOMContentLoaded', function() {
    console.log('loaded');
    let CountdownObject;
    

    if(localStorage.sessionTTL === undefined) {
        console.log('session not alive');
        CountdownObject = new Countdown(SessionLifeTime, SessionResumeLifeTime);
        CountdownObject.createSession();
    } else {
        console.log('session alive');
        const json = JSON.parse(localStorage.sessionTTL);

        const now = new Date().getTime();

        // Find the distance between now and the expiry date.
        const distance = new Date(json.expiry) - now;

        // Calculate remaining seconds
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        // CountdownObject.stopCountDown();
        // CountdownObject.stopTimer();
        CountdownObject = new Countdown(seconds, SessionResumeLifeTime);
        CountdownObject.updateSession();
        // CountdownObject.initiateTimer();
        // CountdownObject.initiateCountDownInterval();
    }

    /**
     * Add event listeners to modal buttons
     */
    document.getElementById('js-resumeSession').addEventListener('click', function() {
        CountdownObject.resumeSession();
    });

    document.getElementById('js-resumeSession').addEventListener('keydown', function() {
        CountdownObject.a11yClick();
    });

    document.getElementById('js-abortSession').addEventListener('click', function() {
        CountdownObject.endSession();
    });

    document.getElementById('js-abortSession').addEventListener('keydown', function() {
        CountdownObject.a11yClick();
    });

    document.getElementById('logoutLink').addEventListener('click', function() {
        (this.clicked != undefined) ? this.clicked = !this.clicked : this.clicked = true;

        CountdownObject.logoutClicked(this.clicked);
        localStorage.removeItem('sessionTTL');
    });

    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById('modalWrapper');
        const keyCode = e.keyCode;

        if (keyCode == KEY_CODE_SPACE && modal.classList.contains('show')) {
            CountdownObject.endSession();
        }

        return;
    });
// });

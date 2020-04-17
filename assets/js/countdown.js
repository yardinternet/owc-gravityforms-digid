/**
 * Declare variables.
 * Add modal to form, outside of the pagination
 */
var timerInterval;
var countDownInterval;
const gravityFormsWrapperDiv = document.getElementsByClassName("gform_anchor"); 
const countDownDiv = document.getElementsByClassName("gform_heading");
if(countDownDiv.length > 0) {
    var initialContent = countDownDiv[0].innerHTML;
}

if(gravityFormsWrapperDiv.length > 0) {
    gravityFormsWrapperDiv[0].innerHTML = `
        <div class='modal fade' id='modalWrapper' tabindex='-1' role='dialog' aria-labelledby='modalWrapper' aria-modal='true' aria-hidden='true' style='display:none;'>
            <div id='modalDialog' class='modal-dialog' role='document'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title' id='exampleModalLabel'>Uw sessie verloopt.</h5>
                    </div>
                    <div class='modal-body | mb-4'>
                        Uw sessie is mogelijk verlopen. Als u te lang niks hebt gedaan, wordt u uit veiligheidsoverwegingen door DigiD uitgelogd.
                        Kies 'Ja' om uw sessie te verlengen, mogelijk moet u opnieuw inloggen met DigiD.
                    </div>
                    <div class='modal-footer | d-flex justify-content-between'>
                        <button type='button' id='js-resumeSession' tabindex='0' role='button' class='btn btn-primary mr-2'>Verlengen</button>
                        <button type='button' id='js-abortSession' tabindex='0' role='button' class='btn btn-secondary' data-dismiss='modal'>Sluiten</button>
                    </div>
                </div>
            </div>
        </div>`;
}

/**
 * Add event listeners to modal buttons
 */
const elementResumeSession = document.getElementById("js-resumeSession");
elementResumeSession.addEventListener('click', function() {
    document.getElementById("js-resumeSession").onclick = resumeSession();
});

elementResumeSession.addEventListener('keydown', function() {
    document.getElementById("js-resumeSession").onclick = a11yClick();
});

const elementAbortSession = document.getElementById("js-abortSession");
elementAbortSession.addEventListener('click', function() {
    document.getElementById("js-abortSession").onclick = endSession();
});

elementAbortSession.addEventListener('keydown', function() {
    document.getElementById("js-abortSession").onclick = a11yClick();
});

/**
 * Create session if there is none.
 * Initiate timer and countdown.
 */
if (undefined === localStorage.sessionTTL) {
    localStorage.sessionTTL = JSON.stringify(createLifeTimeObject());
    initiateTimer();
    initiateCountdown();
}

/**
 * Initiate timer.
 */
function initiateTimer() {
    timerInterval = setInterval(beginTimer, 500);
}

/**
 * Create object that is used for the values of the session.
 */
function createLifeTimeObject() {
    const lifetime = 10;
    return {
        value: lifetime,
        expiry: Date.now() + (lifetime * 1000)
    }
}

/**
 * Create object and return property: expiry.
 * Property is used for validating the session.
 */
function parseJSON() {
    const json = JSON.parse(localStorage.sessionTTL);
    return json.expiry;
}

/**
 * Is used for validating the session.
 */
function beginTimer() {
    if (Date.now() > parseJSON() && localStorage.sessionTTL) {
        openModal()
    }
        
    return;
}

/**
 * Stop timer; fires after the session is expired.
 */
function stopTimer() {
    clearInterval(timerInterval);
}

/**
 * Initiate countdown
 */
function initiateCountdown() {
    countDownInterval = setInterval(countDown, 1000);
}

/**
 * Run countdown.
 */
function countDown() {
    // if there is no session just return.
    if (undefined === localStorage.sessionTTL) {
        return;
    }

    // Get today's date and time.
    const now = new Date().getTime();

    // Find the distance between now and the count down date.
    const distance = new Date(parseJSON()) - now;

    // Time calculations for minutes and seconds.
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    const countDownDiv = document.getElementsByClassName("gform_heading");

    // get current content save it and place it in a new div + plus a new one for countdown
    if(countDownDiv.length > 0) {
        countDownDiv[0].innerHTML = `<div class="d-flex flex-row justify-content-between"><div>${initialContent}</div><div>Resterende tijd: ${minutes}:${seconds}</div></div>`;
    }
}

/**
 * Stop countdown when timer is finished.
 */
function stopCountDown() {
    clearInterval(countDownInterval);
    const countDownDiv = document.getElementsByClassName("gform_heading");

    if(countDownDiv.length > 0) {
        countDownDiv[0].innerHTML = "Verlopen";
    }
}

/**
 * Open modal; user input defines the action to execute.
 */
function openModal() {
    // clear session and stop timer & countdown interval
    localStorage.removeItem("sessionTTL");
    stopTimer();
    stopCountDown();

    // get modal
    const modalWrapper = document.getElementById('modalWrapper');
    const modalDialog = document.getElementById('modalDialog');

    // change state like in hidden modal.
    if(modalWrapper !== null){
        modalWrapper.classList.add('show');
        modalWrapper.setAttribute('aria-hidden', 'false');
        modalWrapper.style.cssText = "display: block; position: fixed; top: 0; bottom: 0; left: 0; right: 0; background-color: #666; opacity: 1; z-index: 1272;";
    }

    if(modalDialog !== null) {
        modalDialog.style.cssText = "max-width: 500px; margin: 5rem auto; background-color: #ffffff; padding: 2rem;";
    }

    startResumeCheck();
}

/**
 * When modal is visible this function launches a timer.
 */
function startResumeCheck() {
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
 * Extend session from modal.
 */
function resumeSession() {
    localStorage.sessionTTL = JSON.stringify(createLifeTimeObject());
    const modal = document.getElementById('modalWrapper');
    const modalDialog = document.getElementById('modalDialog');

    if(modal !== null) {
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
        modal.style.cssText = "display: none;";
    }

    if(modalDialog !== null) {
        modalDialog.style.cssText = "";
    }
    initiateTimer();
    initiateCountdown();
}

/**
 * Exterminate session.
 */
function endSession() {
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
function a11yClick(event){
    if(event.type === 'click'){
        return true;
    }
    else if(event.type === 'keypress'){
        var code = event.charCode || event.keyCode;

        // keycode is 'space'
        if((code === 32)){
            return true;
        }
    }
    else {
        return false;
    }
}

document.addEventListener('keydown', function(e) {
    const modal = document.getElementById("modalWrapper");
    const keyCode = e.keyCode;

    if (keyCode == 27 && modal.classList.contains("show")) {
        endSession();
    }

    return;
});

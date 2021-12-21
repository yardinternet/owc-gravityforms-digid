export default class LogoutButton {
	constructor() {
		this.logoutBtn = document.querySelector('#logoutLink');
		this.logoutBtnCopy = document.querySelector('#logoutLink').cloneNode(true);
		this.gformHeading = document.querySelector('.gform_heading');

		var logoutBtn = document.querySelector('#logoutLink');
		var logoutBtnCopy = document.querySelector('#logoutLink').cloneNode(true);
		var gformHeading = document.querySelector('.gform_heading');

		this.repositionLogoutButton();
	}

	repositionLogoutButton = () => {
		this.logoutBtn.remove();
		this.gformHeading.insertAdjacentElement('afterend', this.logoutBtnCopy);
	}
}
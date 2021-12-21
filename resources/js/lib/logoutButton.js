export default class LogoutButton {
	constructor() {
		this.logoutBtn = document.querySelector('#logoutLink');
		this.logoutBtnCopy = document.querySelector('#logoutLink').cloneNode(true);
		this.timer = document.querySelector('#js-countdown');
	}

	get init() {
		return this.repositionLogoutButton();
	}

	repositionLogoutButton() {
		this.logoutBtn.remove();
		this.timer.insertAdjacentElement('afterend', this.logoutBtnCopy);
	}
}
<div class="owc-gf-digid-toolbar">
	<div class="owc-gf-digid-toolbar-countdown" id="js-owc-gf-digid-countdown"></div>
	<button class="owc-gf-digid-toolbar-logout" data-action="{{ logoutLink }}" id="js-owc-gf-digid-logout">Uitloggen</button>
</div>

<script>
	var SessionLifeTime = '{{ SessionLifeTime }}';
	var SessionResumeLifeTime = '{{ SessionResumeLifeTime }}';

	document.addEventListener('DOMContentLoaded', function() {
		new Countdown.Countdown(SessionLifeTime, SessionResumeLifeTime).init()
		new Countdown.Logout().init();
	});
</script>

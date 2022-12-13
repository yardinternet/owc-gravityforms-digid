<div class="owc-gf-digid-toolbar">
	<div class="owc-gf-digid-toolbar-countdown" id="js-countdown"></div>
	<a class="owc-gf-digid-toolbar-logout" href="{{ logoutLink }}" id="logoutLink">Uitloggen</a>
</div>

<script>
	var SessionLifeTime = '{{ SessionLifeTime }}';
	var SessionResumeLifeTime = '{{ SessionResumeLifeTime }}';

	document.addEventListener('DOMContentLoaded', function() {
		new Countdown.Countdown(SessionLifeTime, SessionResumeLifeTime).init()
		new Countdown.LogoutButton().init();
	});
</script>

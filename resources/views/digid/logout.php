<a id="logoutLink" href="{{ logoutLink }}">Uitloggen</a>

<script>
	var SessionLifeTime = '{{ SessionLifeTime }}';
	var SessionResumeLifeTime = '{{ SessionResumeLifeTime }}';

	document.addEventListener('DOMContentLoaded', function() {
		new Countdown.Countdown(SessionLifeTime, SessionResumeLifeTime).init()
		new Countdown.LogoutButton().init();
	});
</script>

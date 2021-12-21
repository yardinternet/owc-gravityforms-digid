<p class="font-weight-bold">U bent succesvol ingelogd. Klik op de knop 'Volgende'.</p>
<a id="logoutLink" href="{{ logoutLink }}">Uitloggen</a>

<script>
	var SessionLifeTime = '{{ SessionLifeTime }}';
	var SessionResumeLifeTime = '{{ SessionResumeLifeTime }}';

	document.addEventListener('DOMContentLoaded', function() {
		var instance = new Countdown.Countdown(SessionLifeTime, SessionResumeLifeTime);
		instance.init();
		// instance.moveLogoutButton();

		console.log(new LogoutButton());
		var button = new LogoutButton.LogoutButton();
		button.init();
	});
</script>
<a id="logoutLink" href="{{ logoutLink }}">Uitloggen</a>
<p>U bent succesvol ingelogd. Klik op volgende knop.</p>

<script>
    const SessionLifeTime = '{{ SessionLifeTime }}';
    const SessionResumeLifeTime = '{{ SessionResumeLifeTime }}';

	document.addEventListener('DOMContentLoaded', function() {
		var instance = new Countdown(SessionLifeTime, SessionResumeLifeTime);
		instance.init();
	});
</script>

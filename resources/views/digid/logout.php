<div class="owc-gf-digid-toolbar">
	<div class="owc-gf-digid-toolbar-countdown" id="js-owc-gf-digid-countdown"></div>
	<a href="{{ logoutLink }}">
		<button class="owc-gf-digid-toolbar-logout" id="js-owc-gf-digid-logout">Uitloggen</button>
	</a>
</div>

<script>
	var SessionLifeTime = '{{ SessionLifeTime }}';
	var LastActivity = '{{ LastActivity }}';

	document.addEventListener('DOMContentLoaded', function() {
		new CountdownDigiD.CountdownDigiD(SessionLifeTime, LastActivity).init();
	});
</script>

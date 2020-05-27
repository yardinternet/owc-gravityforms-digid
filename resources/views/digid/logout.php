<a id="logoutLink" href="{{ logoutLink }}">Uitloggen</a>

<p>U bent succesvol ingelogd. Klik op volgende knop.</p>

<script>
    const SessionLifeTime = '{{ SessionLifeTime }}';
    const SessionResumeLifeTime = '{{ SessionResumeLifeTime }}';
    const JsSessionFilePath = '{{ JsSessionFilePath }}';
</script>
<script>
    console.log(document.getElementById('JsSessionFilePath'));
    if (!document.getElementById('JsSessionFilePath')) {
        var script = document.createElement('script');
        script.onload = function() {
            //do stuff with the script
        };
        script.src = JsSessionFilePath;
        script.id = "JsSessionFilePath"

        document.head.appendChild(script);
    }
</script>
<!-- <script id="JsSessionFilePath" src="{{ JsSessionFilePath }}"></script> -->
<html>
<head>
</head>
<body style="font-family: Cambria, Georgia, serif; background: #e6e6e6;">
<h3>Hallo {{ mail.to.name }},</h3>
<p>
    {{ mail.actor.name }} hat gerade für die Paarung
    <p style="margin-left: 20px;">
        <b>{{ match.text }}</b>
    </p>
    den folgenden Spieltermin eingetragen:
    <p style="margin-left: 20px;">
        <b>{{ match.fixture }}</b>
    </p>
</p>
<p>
    Sollte es sich hier um einen Fehler handeln, <a style="color: #ff0000;" href="mailto:ligaleitung@kickerligakoeln.de" target="_blank">melde Dich bitte bei uns.</a>
</p>
<p>
    Viele Grüße<br/>
    Ligaleitung<br/>
    Kölner Kickerliga
</p>
</body>
</html>

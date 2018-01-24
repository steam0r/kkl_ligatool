<html>
<head>
</head>
<body style="font-family: Cambria, Georgia, serif; background: #e6e6e6;">
<h3>Liebe Kapitäne,</h3>
<p>
    ein neuer Spieltag startet bald, unten findet ihr die Paarungen. <br/>
    Also höchste Zeit einen Spieltermin zu organisieren und mit eurem Gegner in Kontakt zu treten.
</p>
<p>
    Ligen & Begegnungen:
</p>
<table style="margin-left: 20px;">
    <tr>
        <th style="text-align:left; width: 120px">Liga</th>
        <th style="text-align:left;">Begegnung</th>
        <th style="text-align:left;">Heimspielort</th>
        <th></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    {% set previousleague = "" %}
    {% for match in matches %}
    {% if (previousleague != match.leaguecode) %}
    <tr style="border-top: 1px solid black;">
        <td colspan="5" style="height: 10px; width: 120px"><hr/></td>
    </tr>
    {% endif %}
    <tr>
        <td>
            {{ match.league}}
        </td>
        <td>
            {{ match.title}}
        </td>
        <td>
            {{ match.location}}
        </td>
        <td style="padding-left: 20px;">
            <a style="color: #ff0000;" href="https://www.kickerligakoeln.de/tabelle/{{ match.leaguecode }}/" target="_blank">Tabelle</a>
        </td>
        <td style="padding-left: 10px;">
            <a style="color: #ff0000;" href="https://doodle.com/create?title={{ match.title|url_encode }}&location={{ match.location|url_encode }}" target="_blank">Doodle</a>
        </td>
    </tr>
    {% set previousleague = match.leaguecode %}
    {% endfor %}
    </tbody>
</table>
<p>
    Kontaktdaten der Gegner:<br/>
    <a style="color: #ff0000;" href="https://www.kickerligakoeln.de/spielbetrieb/kontaktliste/" target="_blank">https://www.kickerligakoeln.de/spielbetrieb/kontaktliste/</a>
</p>
<p>
    Viele Grüße<br/>
    Ligaleitung<br/>
    Kölner Kickerliga
</p>
</body>
</html>

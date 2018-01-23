<div class="kkl-kontaktliste">
    <nav class="list-pills">
        <ul>
            <li><a rel="isotope" href="#" data-filter="*">Alle</a></li>
            <li><a rel="isotope" href="#" data-filter=".ligaleitung">Ligaleitung</a></li>
            <li><a rel="isotope" href="#" data-filter=".captain">Kapitän</a></li>
            <li><a rel="isotope" href="#" data-filter=".vice_captain">Vizekapitän</a></li>
            {% for league in leagues %}
                <li><a rel="isotope" href="#" data-filter=".{{ league.code }}" data-id="{{ league.current_season }}">{{ league.name }}</a></li>
            {% endfor %}
        </ul>
    </nav>

    <div class="isotope-grid kontakte-wrapper">

        {% for player in players %}
            <div class="kontakte-item  isotope-item  {{ player.league_short }} {{ player.role }} {{ player.team_short }}">

                <h3>
                    {{ player.first_name }} {{ player.last_name }}<br/>
                    {% if player.team %}
                        <em>"{{ player.team }}"</em>
                    {% endif %}
                </h3>
                <p>
                    {% if player.location %}
                        Spielort: {{ player.location }}<br/>
                    {% endif %}
                    Telefon: {{ player.phone }}<br/>
                    E-Mail: <a href="mailto:{{ player.email }}">{{ player.email }}</a>
                </p>
            </div>
        {% endfor %}
    </div>
</div>

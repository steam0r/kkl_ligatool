<div class="kkl-kontaktliste">
  <nav class="navbar-pills">
    <ul>
      <li><a rel="isotope" href="#" data-filter="*">Alle</a></li>
      <li><a rel="isotope" href="#" data-filter=".ligaleitung">Ligaleitung</a></li>
      <li><a rel="isotope" href="#" data-filter=".captain">Kapitän</a></li>
      <li><a rel="isotope" href="#" data-filter=".vice_captain">Vizekapitän</a></li>
      <li><a rel="isotope" href="#" data-filter=".koeln1">1. Liga</a></li>
      <li><a rel="isotope" href="#" data-filter=".koeln2a">2. Liga A</a></li>
      <li><a rel="isotope" href="#" data-filter=".koeln2b">2. Liga B</a></li>
      <li><a rel="isotope" href="#" data-filter=".koeln3a">3. Liga A</a></li>
      <li><a rel="isotope" href="#" data-filter=".koeln3b">3. Liga B</a></li>
      <li><a rel="isotope" href="#" data-filter=".koeln4a">4. Liga A</a></li>
      <li><a rel="isotope" href="#" data-filter=".koeln4b">4. Liga B</a></li>
      <li><a rel="isotope" href="#" data-filter=".koeln4c">4. Liga C</a></li>
    </ul>
  </nav>

  <div class="isotope-grid kkl-kontaktliste--wrapper">
    {% for player in players %}
      <div class="kkl-kontaktliste--item  isotope-item  {{ player.league_short }} {{ player.role }} {{ player.team_short }}">
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

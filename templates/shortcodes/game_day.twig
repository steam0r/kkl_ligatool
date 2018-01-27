{% if view == 'all' %}
  <h2>{{ context.league.name }} - {{ context.season.name }}</h2>
{% endif %}

{% for schedule in schedules %}
{% if view == 'current' %}
  <h3>Aktueller Spieltag <div style="float: right">{{ schedule.day.fixture|date('d.m.Y') }} bis {{ schedule.day.end|date('d.m.Y') }}</div></h3>
{% else %}
  <h3>Spieltag {{ schedule.number }} <div style="float: right">{{ schedule.day.fixture|date('d.m.Y') }} bis {{ schedule.day.end|date('d.m.Y') }}</div></h3>
{% endif %}
<table class="table table-striped kkl-table">
  <thead>
    <tr>
      <th class="date">Datum</th>
      <th class="home">Heim</th>
      <th class="divider"></th>
      <th class="guest">Gast</th>
      <th class="score">Ergebnis</th>
    </tr>
  </thead>
  <tbody>
    {% for match in schedule.matches %}
    <tr {% if (activeTeam and ((activeTeam == match.home.short_name) or (activeTeam == match.away.short_name))) %}class="active"{% endif %}>
      <td class="date">
        <span class="hidden-xs">{% if match.fixture %}{% if match.fixture != '0000-00-00 00:00:00'%}{{ match.fixture|date('d.m.Y - H:i') }}{% else %}tba{% endif %}{% endif %}</span>
        <span class="visible-xs">{% if match.fixture %}{% if match.fixture != '0000-00-00 00:00:00'%}{{ match.fixture|date('d.m.Y') }}{% else %}tba{% endif %}{% endif %}</span>
      </td>
      <td class="home"><a href="/team/{{ match.home.link }}">{{ match.home.name }}</a></td>
      <td class="divider">gg.</td>
      <td class="guest"><a href="/team/{{ match.away.link }}">{{ match.away.name }}</a></td>
      <td class="score">
        {% if match.status != 3 and (match.score_home == false and match.score_away == false) %}
          -:-
        {% else %}
          {{ match.score_home}}:{{ match.score_away }}
        {% endif %}
      </td>
    </tr>
    {% endfor %}
  </tbody>
  <tfoot>
    <tr>
      <td colspan="5">
        {% if view == 'current' %}
          <a class="showSchedule" href="/spielplan/{{ schedule.link }}">
            <i class="fa fa-calendar"></i>
            Spielplan anzeigen
          </a>
        {% endif %}
      </td>
    </tr>
  </tfoot>
</table>
{% endfor %}

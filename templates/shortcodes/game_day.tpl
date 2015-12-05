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
      <th>Datum</th>
      <th>Heim</th>
      <th></th>
      <th>Gast</th>
      <th>Ergebnis</th>
    </tr>
  </thead>
  <tbody>
    {% for match in schedule.matches %}
    <tr {% if (activeTeam and ((activeTeam == match.home.short_name) or (activeTeam == match.away.short_name))) %}class="active"{% endif %}>
      <td>
        <span class="hidden-xs">{% if match.fixture %}{{ match.fixture|date('d.m.Y - H:i') }}{% else %}tba{% endif %}</span>
        <span class="visible-xs">{% if match.fixture %}{{ match.fixture|date('d.m.Y') }}{% else %}tba{% endif %}</span>
      </td>
      <td><a href="/team/{{ match.home.link }}">{{ match.home.name }}</a></td>
      <td>gg.</td>
      <td><a href="/team/{{ match.away.link }}">{{ match.away.name }}</a></td>
      <td>
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
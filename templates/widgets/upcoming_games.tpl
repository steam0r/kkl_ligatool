{% if league %}<h5>{{ league.name }}</h5>{% endif %}
<ul class="teamBegegnungen">
	{% for match in schedule %}
	<li>
		{% if mark == match.homename %} {% endif %}
		<span class="date">{% if match.fixture %}{{ match.fixture|date('d.m.') }} | {% endif %}</span>
		<span class="home">
		{% if display_result and (match.score_home > match.score_away) %} 
			<b>{{ match.homename }}</b>
		{% else %}
			{{ match.homename }}
		{% endif %}
		</span>
		-
		<span class="guest">
		{% if display_result and (match.score_home < match.score_away) %} 
			<b>{{ match.awayname }}</b>
		{% else %}
			{{ match.awayname }}
		{% endif %}
		</span>
		{% if display_result and not (match.score_home == 0 and match.score_away == 0)%} - <span class="home">{{ match.score_home }}</span> : <span class="guest">{{ match.score_away }}</span>{% endif %}
	</li>
	{% endfor %}
</ul>
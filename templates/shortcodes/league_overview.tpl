<div class="isotope-grid kkl-teams text-center">
{% for league in leagues%}
	{% for team in league.teams %}
		<div class="col-xs-4 col-sm-3 isotope-item {{ league.code }}">
			<a href="/team/{{ team.link }}" target="_self" title="{{ team.name }}">
				<img style="width: 200px; height: 150px;" class="img-responsive" src="{{ team.logo }}" alt="KKL |{{ team.name }}" />
				<span>{{ team.name }}</span>
			</a>
		</div>
	{% endfor %}
{% endfor %}
</div>

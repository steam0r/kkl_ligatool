{% for player in players %}
<div class="cn-list-row vcard individual">																					
	<div class="col-xs-12 col-sm-6 col-lg-4 isotope-item">
        <div class="kontakt-item">
          <h3>
						<span class="fn n notranslate"> 
							<span class="given-name">{{ player.first_name }}</span> 
							<span class="family-name">{{ player.last_name }}</span> 
						</span>
					</h3>
          <p>
          	"<em>TEAM</em>"</p><p>Spielort: ORT</p>
          <p></p>
          <p>
            Telefon: {{ player.phone }}<br>
            E-Mail: <a href="mailto:{{ player.email }}">{{ player.email }}</a>
          </p>
          <p>
            <a href="#" rel="isotope" data-filter=".koeln4a" class="label label-kkl">LIGA</a><a href="#" rel="isotope" data-filter=".captain" class="label label-kkl">ROLLE</a>          </p>
        </div>
      </div>

			
</div>

{% endfor %}

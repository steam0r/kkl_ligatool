<?php

namespace KKL\Ligatool;


class Pages {

  const PAGES_PATH = 'pages';

  /**
   *
   * @return mixed
   */
  public static function leagueOverview() {
    $kkl_twig = Template\Service::getTemplateEngine();
    $db = new DB\Wordpress();

    $all_leagues = $db->getActiveLeagues();
    $leagues = array();
    foreach($all_leagues as $league) {
      $league->season = $db->getSeason($league->current_season);
      $league->teams = $db->getTeamsForSeason($league->season->id);
      foreach($league->teams as $team) {
        $club = $db->getClub($team->club_id);
        if(!$team->logo) {
          $team->logo = $club->logo;
          if(!$club->logo) {
            $team->logo = "";
          }
        } else {
          $team->logo = "/images/team/" . $team->logo;
        }
        // HACK
        $team->link = Plugin::getLink('club', array('club' => $club->short_name));
      }

      $day = $db->getGameDay($league->season->current_game_day);

      $league->link = Plugin::getLink('league', array(
          'league' => $league->code,
          'season' => date('Y', strtotime($league->season->start_date)),
          'game_day' => $day->number,
      ));
      $leagues[] = $league;
    }

    return $kkl_twig->render(
        self::PAGES_PATH . '/league_overview.twig',
        array(
            'leagues' => $leagues,
            'all_leagues' => $all_leagues
        )
    );
  }


  /**
   *
   * @return mixed
   */
  public static function contactList() {
    $kkl_twig = Template\Service::getTemplateEngine();

    $db = new DB\Wordpress();

    $leagues = $db->getActiveLeagues();
    $leagueadmins = $db->getLeagueAdmins();
    $captains = $db->getCaptainsContactData();
    $vicecaptains = $db->getViceCaptainsContactData();
    $players = array_merge($leagueadmins, $captains, $vicecaptains);

    $leagueMap = array();
    foreach($leagues as $league) {
      $leagueMap[$league->code] = $league;
    }

    $contactMap = array();
    foreach($players as $player) {

      if($player->league_short) {
        if(!isset($contactMap[$player->league_short])) {
          $contactMap[$player->league_short]['league'] = $leagueMap[$player->league_short];
        }
        $contactMap[$player->league_short]['players'][] = $player;
      } else if($player->role === 'ligaleitung') {
        if(!isset($contactMap['ligaleitung'])) {
          $contactMap['ligaleitung']['league'] = array(
              'id' => 'ligaleitung',
              'code' => 'ligaleitung',
              'name' => 'Ligaleitung'
          );
        }

        $contactMap['ligaleitung']['players'][] = $player;
      }
    }

    return $kkl_twig->render(
        self::PAGES_PATH . '/contact_list.twig', array(
            'leagues' => $leagues,
            'contactMap' => $contactMap
        )
    );

  }
}